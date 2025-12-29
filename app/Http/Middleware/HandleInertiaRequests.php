<?php

namespace App\Http\Middleware;

use App\Models\Kitchen;
use App\Models\UserShare;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $authUser = $request->user();

        $kitchens = null;

        if ($authUser) {
            $owner = $authUser->kitchenOwner();
            $activeKitchenId = $request->session()->get('active_kitchen_id');
            $activeKitchen = $owner->activeKitchen($activeKitchenId ? (int) $activeKitchenId : null);

            if ($activeKitchen) {
                $request->session()->put('active_kitchen_id', $activeKitchen->id);
            }

            $kitchens = [
                'current' => $activeKitchen ? [
                    'id'       => $activeKitchen->id,
                    'name'     => $activeKitchen->name,
                    'color'    => $activeKitchen->color,
                    'owner_id' => $activeKitchen->owner_id,
                ] : null,
                'available' => $this->availableKitchens($authUser)
                    ->map(fn ($kitchen) => [
                        'id'       => $kitchen->id,
                        'name'     => $kitchen->name,
                        'owner_id' => $kitchen->owner_id,
                        'color'    => $kitchen->color,
                    ]),
                'owner_id' => $authUser->kitchenOwnerId(),
            ];
        }

        return array_merge(parent::share($request), [
            'app' => [
                'name' => config('app.name'),
                'url' => rtrim(config('app.url'), '/'),
            ],
            'auth' => [
                'user' => fn () => $authUser
                    ? [
                        'id'       => $authUser->id,
                        'name'     => $authUser->name,
                        'email'    => $authUser->email,
                        'is_admin' => (bool) ($authUser->is_admin ?? false),
                    ]
                    : null,
            ],
            'kitchens' => fn () => $kitchens,
        ]);
    }

    protected function availableKitchens($user)
    {
        $owned = $user->ownedKitchens()->get(['id', 'name', 'owner_id', 'color', 'icon']);
        $member = $user->kitchens()->get(['kitchens.id', 'kitchens.name', 'kitchens.owner_id', 'kitchens.color', 'kitchens.icon']);

        $sharedOwnerIds = UserShare::where('invited_user_id', $user->id)
            ->pluck('owner_id');

        $shared = Kitchen::whereIn('owner_id', $sharedOwnerIds)
            ->get(['id', 'name', 'owner_id', 'color', 'icon']);

        return $owned
            ->concat($member)
            ->concat($shared)
            ->unique('id')
            ->values();
    }
}
