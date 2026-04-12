<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required'              => 'Le champ nom est obligatoire.',
            'name.max'                   => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.required'             => 'L\'adresse e-mail est obligatoire.',
            'email.email'                => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique'               => 'Cette adresse e-mail est déjà utilisée.',
            'password.required'          => 'Le mot de passe est obligatoire.',
            'password.confirmed'         => 'Les deux mots de passe ne correspondent pas.',
            'password.min'               => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.mixed'             => 'Le mot de passe doit contenir au moins une majuscule et une minuscule.',
            'password.numbers'           => 'Le mot de passe doit contenir au moins un chiffre.',
            'password.symbols'           => 'Le mot de passe doit contenir au moins un caractère spécial.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
