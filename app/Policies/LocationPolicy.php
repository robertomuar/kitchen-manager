<?php

namespace App\Policies;

use App\Models\Location;
use App\Models\User;

class LocationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->kitchenOwner()->currentKitchen() !== null;
    }

    public function view(User $user, Location $location): bool
    {
        return $this->ownsActiveKitchenResource($user, $location);
    }

    public function create(User $user): bool
    {
        return $user->kitchenOwner()->currentKitchen() !== null;
    }

    public function update(User $user, Location $location): bool
    {
        return $this->ownsActiveKitchenResource($user, $location);
    }

    public function delete(User $user, Location $location): bool
    {
        return $this->ownsActiveKitchenResource($user, $location);
    }

    protected function ownsActiveKitchenResource(User $user, Location $location): bool
    {
        $ownerId   = $user->kitchenOwnerId();
        $kitchenId = $user->kitchenOwner()->currentKitchen()?->id;

        if ($location->user_id !== $ownerId) {
            return false;
        }

        return $kitchenId === null || $location->kitchen_id === $kitchenId;
    }
}
