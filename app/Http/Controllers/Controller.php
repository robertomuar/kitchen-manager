<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

abstract class Controller
{
    /**
     * Determina el dueño y la cocina activa para el usuario autenticado.
     */
    protected function resolveKitchenContext(Request $request): array
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $owner = $user->kitchenOwner();
        $activeKitchenId = $request->session()->get('active_kitchen_id');
        $kitchen = $owner->activeKitchen($activeKitchenId ? (int) $activeKitchenId : null);

        abort_if($kitchen === null, 403, 'No hay una cocina activa seleccionada.');

        $request->session()->put('active_kitchen_id', $kitchen->id);

        return [$owner, (int) $owner->id, (int) $kitchen->id];
    }

    /**
     * Limpia la caché asociada al dashboard de la cocina actual.
     */
    protected function forgetDashboardCache(int $ownerId, int $kitchenId): void
    {
        Cache::forget("dashboard.stats.{$ownerId}.{$kitchenId}");
        Cache::forget("dashboard.low-stock.{$ownerId}.{$kitchenId}");
        Cache::forget("dashboard.soon-expiring.{$ownerId}.{$kitchenId}");
    }
}
