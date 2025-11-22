<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserShare;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class KitchenShareController extends Controller
{
    /**
     * Invitar a otro usuario a usar mi cocina.
     */
    public function store(Request $request): RedirectResponse
    {
        $owner = $request->user();

        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $invited = User::where('email', $data['email'])->firstOrFail();

        if ($invited->id === $owner->id) {
            return back()->withErrors([
                'email' => 'No puedes compartir contigo mismo.',
            ]);
        }

        $share = UserShare::firstOrCreate(
            [
                'owner_id'        => $owner->id,
                'invited_user_id' => $invited->id,
            ],
            [
                'can_edit' => true,
            ]
        );

        // Si el invitado no está actuando como nadie, lo ponemos a usar tu cocina.
        if ($invited->acting_as_user_id === null) {
            $invited->acting_as_user_id = $owner->id;
            $invited->save();
        }

        return back()->with('success', 'Has compartido tu cocina con ' . $invited->email);
    }

    /**
     * Revocar acceso a mi cocina.
     */
    public function destroy(Request $request, UserShare $share): RedirectResponse
    {
        $owner = $request->user();

        if ($share->owner_id !== $owner->id) {
            abort(403);
        }

        $invited = $share->invitedUser;

        $share->delete();

        // Si el invitado estaba usando esta cocina, lo devolvemos a la suya
        if ($invited && $invited->acting_as_user_id === $owner->id) {
            $invited->acting_as_user_id = null;
            $invited->save();
        }

        return back()->with('success', 'Has revocado el acceso a tu cocina.');
    }
}
