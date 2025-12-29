<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->kitchenOwner()->currentKitchen() !== null;
    }

    public function view(User $user, Product $product): bool
    {
        return $this->ownsActiveKitchenResource($user, $product);
    }

    public function create(User $user): bool
    {
        return $user->kitchenOwner()->currentKitchen() !== null;
    }

    public function update(User $user, Product $product): bool
    {
        return $this->ownsActiveKitchenResource($user, $product);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->ownsActiveKitchenResource($user, $product);
    }

    protected function ownsActiveKitchenResource(User $user, Product $product): bool
    {
        $ownerId   = $user->kitchenOwnerId();
        $kitchenId = $user->kitchenOwner()->currentKitchen()?->id;

        if ($product->user_id !== $ownerId) {
            return false;
        }

        return $kitchenId === null || $product->kitchen_id === $kitchenId;
    }
}
