<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Affiche le profil de l'utilisateur connecté
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Affiche le formulaire d’édition du profil
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Met à jour le profil utilisateur
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'pseudo' => 'required|string|max:255|unique:users,pseudo,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'age' => 'required|integer|min:17',
            'description' => 'nullable|string|max:1000',
            'centre_interet' => 'nullable|string|max:255',
            'profession' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gestion de la photo
        if ($request->hasFile('photo')) {
            // Supprimer l’ancienne s’il y en a une
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $path = $request->file('photo')->store('photos', 'public');
            $validated['photo'] = $path;
        }

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profil mis à jour avec succès ');
    }
}
