<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    // 🔹 Affiche le formulaire de connexion / inscription
    public function showForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }

        return view('auth.register_progressive');
    }

    // 🔹 Inscription progressive
    public function register(Request $request)
    {
        $request->validate([
            'pseudo' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'age' => 'required|integer|min:16',
            'password' => 'required|confirmed|min:6',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
        }

        $user = User::create([
            'pseudo' => $request->pseudo,
            'email' => $request->email,
            'age' => $request->age,
            'description' => $request->description,
            'centre_interet' => $request->centre_interet,
            'profession' => $request->profession,
            'password' => Hash::make($request->password),
            'photo' => $path,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard.index')->with('success', 'Bienvenue, ' . $user->pseudo . ' 🎉');
    }

    // 🔹 Connexion par pseudo
    public function loginPseudo(Request $request)
    {
        $credentials = $request->validate([
            'pseudo' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('pseudo', $credentials['pseudo'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'pseudo' => 'Pseudo ou mot de passe incorrect.',
            ])->onlyInput('pseudo');
        }

        Auth::login($user);
        return redirect()->route('dashboard.index')->with('success', 'Content de te revoir, ' . $user->pseudo . ' 👋');
    }

    // 🔹 Déconnexion
    public function logout()
    {
        Auth::logout();
        return redirect()->route('register.form')->with('success', 'Vous êtes maintenant déconnecté 👋');
    }

    // 🔹 Vérification Ajax du pseudo
    public function checkPseudo(Request $request)
    {
        $request->validate([
            'pseudo' => 'required|string|max:255',
        ]);

        $exists = User::where('pseudo', $request->pseudo)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Ce pseudo existe déjà, choisissez un autre.' : 'Pseudo valide !'
        ]);
    }
}
