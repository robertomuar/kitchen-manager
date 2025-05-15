<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
{
$data = $request->validate([
    'name'          => 'required|string|max:255',
    'email'         => 'required|email|max:255',
    // Ahora admitimos hasta 40 MB y solo jpg/png
    'profile_image' => 'nullable|image|mimes:jpeg,png|max:40960', // 40960 KB = 40 MB
]);
    // Procesar imagen si se sube
    if ($request->hasFile('profile_image')) {
        $path = $request->file('profile_image')
                        ->store('profiles', 'public');
        $data['profile_image'] = $path;
    }

    Auth::user()->update($data);

    return back()->with('status', 'Perfil actualizado con éxito');
}


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
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
