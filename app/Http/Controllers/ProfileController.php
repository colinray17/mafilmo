<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    // Afficher la page profil
    public function index()
    {
        return view('profile', ['user' => Auth::user()]);
    }

    // Mettre à jour prénom + email
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;

        // Si l'email change → email non vérifié
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with('success_profile', 'Profil mis à jour');
    }

    // Mettre à jour le mot de passe
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required'          => 'Le mot de passe actuel est obligatoire.',
            'current_password.current_password'  => 'Le mot de passe actuel est incorrect.',
            'password.required'                  => 'Le nouveau mot de passe est obligatoire.',
            'password.confirmed'                 => 'Les deux mots de passe ne correspondent pas.',
            'password.min'                       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.mixed'                     => 'Le mot de passe doit contenir au moins une majuscule et une minuscule.',
            'password.numbers'                   => 'Le mot de passe doit contenir au moins un chiffre.',
            'password.symbols'                   => 'Le mot de passe doit contenir au moins un caractère spécial.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success_password', 'Mot de passe mis à jour');
    }
}
