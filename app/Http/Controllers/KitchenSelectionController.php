<?php

namespace App\Http\Controllers;

use App\Models\Kitchen;
use App\Models\UserShare;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KitchenSelectionController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kitchen_id' => ['required', 'integer', 'exists:kitchens,id'],
        ]);

        $user = $request->user();
        $kitchen = Kitchen::findOrFail($data['kitchen_id']);

        $canAccess = $kitchen->owner_id === $user->id
            || $kitchen->users()->where('users.id', $user->id)->exists()
            || UserShare::where('owner_id', $kitchen->owner_id)
                ->where('invited_user_id', $user->id)
                ->exists();

        abort_unless($canAccess, 403);

        DB::transaction(function () use ($user, $kitchen, $request) {
            $user->acting_as_user_id = $kitchen->owner_id === $user->id
                ? null
                : $kitchen->owner_id;
            $user->save();

            $request->session()->put('active_kitchen_id', $kitchen->id);
        });

        return back()->with('success', 'Has cambiado a la cocina: ' . $kitchen->name);
    }
}
