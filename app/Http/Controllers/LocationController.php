<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class LocationController extends Controller
{
    /**
     * Listado de ubicaciones del usuario.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $locations = Cache::remember(
            "locations:list:user:{$user->id}",
            300,
            function () use ($user) {
                return $user->locations()
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get();
            }
        );

        return Inertia::render('Locations/Index', [
            'locations' => $locations,
        ]);
    }

    /**
     * Formulario de creación de ubicación.
     */
    public function create(Request $request): Response
    {
        $user = $request->user();

        $nextSortOrder = ($user->locations()->max('sort_order') ?? 0) + 1;

        return Inertia::render('Locations/Form', [
            'mode'          => 'create',
            'location'      => null,
            'nextSortOrder' => $nextSortOrder,
        ]);
    }

    /**
     * Guardar nueva ubicación.
     */
    public function store(LocationRequest $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validated();
        $data['user_id'] = $user->id;

        // Si no viene is_active, la ponemos activa por defecto
        if (!array_key_exists('is_active', $data)) {
            $data['is_active'] = true;
        }

        // Si no viene sort_order, lo colocamos al final
        if (
            !array_key_exists('sort_order', $data)
            || $data['sort_order'] === null
            || $data['sort_order'] === ''
        ) {
            $data['sort_order'] = ($user->locations()->max('sort_order') ?? 0) + 1;
        }

        Location::create($data);
        Cache::forget("locations:list:user:{$user->id}");
        Cache::forget("locations:list:owner:{$user->kitchenOwnerId()}");

        return redirect()
            ->route('locations.index')
            ->with('success', 'Ubicación creada correctamente.');
    }

    /**
     * Formulario de edición.
     */
    public function edit(Request $request, Location $location): Response
    {
        $user = $request->user();

        if ($location->user_id !== $user->id) {
            abort(403);
        }

        return Inertia::render('Locations/Form', [
            'mode'     => 'edit',
            'location' => $location,
        ]);
    }

    /**
     * ✅ NUEVO: evita 405 en GET/HEAD /locations/{id}
     */
    public function show(Request $request, Location $location)
    {
        $user = $request->user();

        if ($location->user_id !== $user->id) {
            abort(403);
        }

        if ($request->header('X-Inertia')) {
            return Inertia::location(route('locations.edit', $location->id));
        }

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json($location);
        }

        return redirect()->route('locations.edit', $location->id);
    }

    /**
     * Actualizar ubicación.
     */
    public function update(LocationRequest $request, Location $location): RedirectResponse
    {
        $user = $request->user();

        if ($location->user_id !== $user->id) {
            abort(403);
        }

        $data = $request->validated();

        // Si por cualquier motivo no viniera, la forzamos a false (inactiva)
        if (!array_key_exists('is_active', $data)) {
            $data['is_active'] = false;
        }

        $location->update($data);
        Cache::forget("locations:list:user:{$user->id}");
        Cache::forget("locations:list:owner:{$user->kitchenOwnerId()}");

        return redirect()
            ->route('locations.index')
            ->with('success', 'Ubicación actualizada correctamente.');
    }

    /**
     * Borrar ubicación.
     */
    public function destroy(Request $request, Location $location): RedirectResponse
    {
        $user = $request->user();

        if ($location->user_id !== $user->id) {
            abort(403);
        }

        $location->delete();
        Cache::forget("locations:list:user:{$user->id}");
        Cache::forget("locations:list:owner:{$user->kitchenOwnerId()}");

        return redirect()
            ->route('locations.index')
            ->with('success', 'Ubicación eliminada correctamente.');
    }
}
