<?php

namespace Tests\Feature;

use App\Models\Kitchen;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StockUnitsTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithKitchen(): array
    {
        $user = User::factory()->create();

        $kitchen = Kitchen::create([
            'name'      => 'Cocina de ' . $user->id,
            'owner_id'  => $user->id,
            'slug'      => 'cocina-' . $user->id . '-' . Str::random(6),
            'color'     => null,
            'icon'      => null,
        ]);

        $user->kitchens()->attach($kitchen->id, ['role' => 'owner']);

        return [$user, $kitchen];
    }

    private function createProduct(User $user, Kitchen $kitchen, string $name = 'Producto demo'): Product
    {
        return Product::create([
            'user_id'          => $user->id,
            'kitchen_id'       => $kitchen->id,
            'name'             => $name,
            'default_quantity' => 1,
            'default_unit'     => 'unidad',
        ]);
    }

    public function test_available_units_are_used_for_low_stock_flag(): void
    {
        [$user, $kitchen] = $this->createUserWithKitchen();
        $product = $this->createProduct($user, $kitchen);

        $item = StockItem::create([
            'user_id'      => $user->id,
            'kitchen_id'   => $kitchen->id,
            'product_id'   => $product->id,
            'quantity'     => 2,
            'open_units'   => 1,
            'unit'         => 'unidad',
            'min_quantity' => 2,
        ]);

        $this->actingAs($user)
            ->withSession(['active_kitchen_id' => $kitchen->id]);

        $item->refresh();

        $this->assertSame(1.0, $item->available_units);
        $this->assertTrue($item->is_below_minimum);
    }

    public function test_open_units_cannot_exceed_quantity(): void
    {
        [$user, $kitchen] = $this->createUserWithKitchen();
        $product = $this->createProduct($user, $kitchen);

        $response = $this->actingAs($user)
            ->withSession(['active_kitchen_id' => $kitchen->id])
            ->post('/stock', [
                'product_id'  => $product->id,
                'quantity'    => 1,
                'open_units'  => 2,
                'unit'        => 'unidad',
                'min_quantity' => 1,
                'notes'       => 'prueba',
                'is_open'     => false,
            ]);

        $response->assertSessionHasErrors('open_units');
    }

    public function test_export_missing_uses_available_units(): void
    {
        [$user, $kitchen] = $this->createUserWithKitchen();
        $product = $this->createProduct($user, $kitchen);

        StockItem::create([
            'user_id'      => $user->id,
            'kitchen_id'   => $kitchen->id,
            'product_id'   => $product->id,
            'quantity'     => 2,
            'open_units'   => 1,
            'unit'         => 'unidad',
            'min_quantity' => 2,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['active_kitchen_id' => $kitchen->id])
            ->get('/stock/export/missing.csv');

        $response->assertOk();
        $this->assertStringContainsString('1,00', $response->streamedContent()); // Falta 1 porque hay 1 abierta
    }

    public function test_multi_tenant_scoping_is_preserved(): void
    {
        [$userA, $kitchenA] = $this->createUserWithKitchen();
        $productA = $this->createProduct($userA, $kitchenA, 'Producto cocina A');

        [$userB, $kitchenB] = $this->createUserWithKitchen();
        $productB = $this->createProduct($userB, $kitchenB, 'Producto cocina B');

        StockItem::create([
            'user_id'      => $userA->id,
            'kitchen_id'   => $kitchenA->id,
            'product_id'   => $productA->id,
            'quantity'     => 0,
            'open_units'   => 0,
            'unit'         => 'unidad',
            'min_quantity' => 1,
        ]);

        StockItem::create([
            'user_id'      => $userB->id,
            'kitchen_id'   => $kitchenB->id,
            'product_id'   => $productB->id,
            'quantity'     => 0,
            'open_units'   => 0,
            'unit'         => 'unidad',
            'min_quantity' => 1,
        ]);

        $responseA = $this->actingAs($userA)
            ->withSession(['active_kitchen_id' => $kitchenA->id])
            ->get('/stock/export/missing.csv');

        $responseA->assertOk();
        $content = $responseA->streamedContent();

        $this->assertStringContainsString($productA->name, $content);
        $this->assertStringNotContainsString($productB->name, $content);
    }
}
