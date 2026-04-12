@extends('layouts.mafilmo')
@section('title', 'Mon profil')

@section('content')

{{-- Header --}}
<x-header />

<div class="container py-4 py-md-5" style="max-width:680px;">

    {{-- En-tête --}}
    <div class="d-flex align-items-center gap-4 mb-5">
        <div class="avatar-large">{{ $user->initials() }}</div>
        <div>
            <h1 class="profile-name">{{ $user->name }}</h1>
            <p style="font-size:15px; color:#6B7280; margin:0;">{{ $user->email }}</p>
        </div>
    </div>

    {{-- Section 1 : Informations personnelles --}}
    <div class="card">
        <h2>👤 Informations personnelles</h2>

        @if(session('success_profile'))
            <div class="alert-success-custom">{{ session('success_profile') }}</div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf @method('PATCH')

            <div class="form-group">
                <label for="name">Prénom</label>
                <input type="text" id="name" name="name"
                       class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                       value="{{ old('name', $user->name) }}"
                       placeholder="Votre prénom">
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email"
                       class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                       value="{{ old('email', $user->email) }}"
                       placeholder="votre@email.com">
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-save">
                Enregistrer les modifications
            </button>
        </form>
    </div>

    {{-- Section 2 : Mot de passe --}}
    <div class="card">
        <h2>🔒 Changer le mot de passe</h2>

        @if(session('success_password'))
            <div class="alert-success-custom">{{ session('success_password') }}</div>
        @endif

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf @method('PATCH')

            <div class="form-group">
                <label for="current_password">Mot de passe actuel</label>
                <input type="password" id="current_password"
                       name="current_password"
                       class="{{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                       placeholder="••••••••">
                @error('current_password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
                <input type="password" id="password" name="password"
                       class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                       placeholder="••••••••">
                <span class="password-hint">
                    Minimum 8 caractères, une majuscule, un chiffre et un caractère spécial
                </span>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
                <input type="password" id="password_confirmation"
                       name="password_confirmation"
                       placeholder="••••••••">
            </div>

            <button type="submit" class="btn-save">
                Mettre à jour le mot de passe
            </button>
        </form>
    </div>

</div>

@endsection
