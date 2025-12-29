<?php

namespace Tests\Feature;

use App\Models\Kitchen;
use App\Models\Location;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OptionsEndpointsTest extends TestCase
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

    public function test_products_options_endpoint_returns_ok(): void
    {
        [$user, $kitchen] = $this->createUserWithKitchen();

        Product::create([
            'user_id'          => $user->id,
            'kitchen_id'       => $kitchen->id,
            'name'             => 'Arroz',
            'default_quantity' => 1,
            'default_unit'     => 'unidad',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['active_kitchen_id' => $kitchen->id])
            ->get('/products/options');

        $response->assertStatus(200);
    }

    public function test_locations_options_endpoint_returns_ok(): void
    {
        [$user, $kitchen] = $this->createUserWithKitchen();

        Location::create([
            'user_id'    => $user->id,
            'kitchen_id' => $kitchen->id,
            'name'       => 'Despensa',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['active_kitchen_id' => $kitchen->id])
            ->get('/locations/options');

        $response->assertStatus(200);
    }
}
