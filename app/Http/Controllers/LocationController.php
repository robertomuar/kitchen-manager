<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LocationController extends Controller
{
    /**
     * Listado de ubicaciones del usuario.
     */
    public function index(Request $request): Response
    {
        [$owner, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $locations = Location::where('user_id', $ownerId)
            ->where('kitchen_id', $kitchenId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Locations/Index', [
            'locations' => $locations,
        ]);
    }

    /**
     * Formulario de creación de ubicación.
     */
    public function create(Request $request): Response
    {
        [$owner, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $nextSortOrder = (Location::where('user_id', $ownerId)
            ->where('kitchen_id', $kitchenId)
            ->max('sort_order') ?? 0) + 1;

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
        [, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $data = $request->validated();
        $data['user_id'] = $ownerId;
        $data['kitchen_id'] = $kitchenId;

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
            $data['sort_order'] = (Location::where('user_id', $ownerId)
                ->where('kitchen_id', $kitchenId)
                ->max('sort_order') ?? 0) + 1;
        }

        Location::create($data);

        $this->forgetDashboardCache($ownerId, $kitchenId);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Ubicación creada correctamente.');
    }

    /**
     * Formulario de edición.
     */
    public function edit(Request $request, Location $location): Response
    {
        [, , $kitchenId] = $this->resolveKitchenContext($request);

        $this->authorize('view', $location);

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
        $this->authorize('view', $location);

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
        [, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $this->authorize('update', $location);

        $data = $request->validated();

        // Si por cualquier motivo no viniera, la forzamos a false (inactiva)
        if (!array_key_exists('is_active', $data)) {
            $data['is_active'] = false;
        }

        $location->update($data + [
            'user_id'    => $ownerId,
            'kitchen_id' => $kitchenId,
        ]);

        $this->forgetDashboardCache($ownerId, $kitchenId);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Ubicación actualizada correctamente.');
    }

    /**
     * Borrar ubicación.
     */
    public function destroy(Request $request, Location $location): RedirectResponse
    {
        [, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $this->authorize('delete', $location);

        $location->delete();

        $this->forgetDashboardCache($ownerId, $kitchenId);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Ubicación eliminada correctamente.');
    }

    /**
     * Opciones paginadas para selects asíncronos.
     */
    public function options(Request $request)
    {
        [, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $search = trim((string) $request->query('search', ''));

        $query = Location::where('user_id', $ownerId)
            ->where('kitchen_id', $kitchenId);

        if ($search !== '') {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15, [
                'id',
                'name',
                'color',
            ]);
    }
}
