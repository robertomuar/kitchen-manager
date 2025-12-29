<?php

namespace App\Policies;

use App\Models\StockItem;
use App\Models\User;

class StockItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->kitchenOwner()->currentKitchen() !== null;
    }

    public function view(User $user, StockItem $stockItem): bool
    {
        return $this->ownsActiveKitchenResource($user, $stockItem);
    }

    public function create(User $user): bool
    {
        return $user->kitchenOwner()->currentKitchen() !== null;
    }

    public function update(User $user, StockItem $stockItem): bool
    {
        return $this->ownsActiveKitchenResource($user, $stockItem);
    }

    public function delete(User $user, StockItem $stockItem): bool
    {
        return $this->ownsActiveKitchenResource($user, $stockItem);
    }

    protected function ownsActiveKitchenResource(User $user, StockItem $stockItem): bool
    {
        $ownerId   = $user->kitchenOwnerId();
        $kitchenId = $user->kitchenOwner()->currentKitchen()?->id;

        if ($stockItem->user_id !== $ownerId) {
            return false;
        }

        return $kitchenId === null || $stockItem->kitchen_id === $kitchenId;
    }
}
