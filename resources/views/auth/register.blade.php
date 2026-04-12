@extends('layouts.mafilmo')
@section('title', 'Inscription')
@section('body-class', 'auth-body')

@section('content')

{{-- Cercles décoratifs --}}
<div class="bg-circle bg-circle-1"></div>
<div class="bg-circle bg-circle-2"></div>
<div class="bg-circle bg-circle-3"></div>
<div class="bg-circle bg-circle-4"></div>

{{-- Logo --}}
<div class="auth-logo">Ma<span>Filmo</span></div>
<div class="auth-tagline">Votre journal de cinéma personnel</div>

{{-- Carte --}}
<div class="form-card">
    <h1 class="form-title">Inscription</h1>

    {{-- Erreurs globales --}}
    @if ($errors->any())
        <div class="alert alert-danger rounded-3 mb-4">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Prénom --}}
        <div class="form-group">
            <label for="name" class="form-label">Prénom</label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-control @error('name') is-invalid @enderror"
                placeholder="Votre prénom"
                value="{{ old('name') }}"
                autocomplete="given-name"
                autofocus
            >
            @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label for="email" class="form-label">Adresse email</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="votre@email.com"
                value="{{ old('email') }}"
                autocomplete="email"
            >
            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Mot de passe --}}
        <div class="form-group">
            <label for="password" class="form-label">Mot de passe</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="••••••••"
                autocomplete="new-password">
            <span class="password-hint">
                Minimum 8 caractères, une majuscule, un chiffre et un caractère spécial
            </span>
            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Confirmation mot de passe --}}
        <div class="form-group-last">
            <label for="password_confirmation" class="form-label">
                Confirmer le mot de passe
            </label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                placeholder="••••••••"
                autocomplete="new-password"
            >
            @error('password_confirmation')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Bouton --}}
        <button type="submit" class="btn-register">
            Créer mon compte
        </button>

        {{-- Séparateur --}}
        <div class="separator">
            <hr><span>ou</span><hr>
        </div>

        {{-- Lien connexion --}}
        <div class="login-box">
            Déjà un compte ?
            <a href="{{ route('login') }}">Se connecter</a>
        </div>

    </form>
</div>

{{-- Footer --}}
<div class="auth-footer">
    © 2026 MaFilmo
    <a href="#">Conditions</a>
    <a href="#">Confidentialité</a>
</div>

@endsection
