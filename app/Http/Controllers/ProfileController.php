<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserShare;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Mostrar perfil + configuración + compartir cocina.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();

        $sharedUsers = $user->sharedUsers()
            ->with('invitedUser')
            ->get()
            ->map(function (UserShare $share) {
                return [
                    'id'   => $share->id,
                    'user' => [
                        'id'    => $share->invitedUser->id,
                        'name'  => $share->invitedUser->name,
                        'email' => $share->invitedUser->email,
                    ],
                ];
            });

        return Inertia::render('Profile/Show', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status'          => session('status'),
            'kitchenSharing'  => [
                'sharedUsers' => $sharedUsers,
            ],
        ]);
    }

    /**
     * Actualizar datos básicos.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Borrar cuenta.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
