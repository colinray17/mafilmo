@extends('layouts.mafilmo')
@section('title', 'Connexion')
@section('body-class', 'auth-body')

@section('content')

<div class="bg-circle bg-circle-1"></div>
<div class="bg-circle bg-circle-2"></div>
<div class="bg-circle bg-circle-3"></div>
<div class="bg-circle bg-circle-4"></div>

{{-- Logo --}}
<div class="auth-logo">Ma<span>Filmo</span></div>
<div class="auth-tagline">Votre journal de cinéma personnel</div>

{{-- Carte --}}
<div class="form-card">
    <h1 class="form-title">Connexion</h1>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 mb-4">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

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
                autofocus
            >
        </div>

        {{-- Mot de passe --}}
        <div class="form-group-sm">
            <label for="password" class="form-label">Mot de passe</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="••••••••"
                autocomplete="current-password"
            >
        </div>

        {{-- Mot de passe oublié --}}
        <div class="form-group-forgot">
            {{-- Lien mot de passe oublié désactivé (nécessite configuration email) --}}
            <a href="#" class="forgot-link" onclick="document.getElementById('pwd-msg').style.display='block'; return false;">
                Mot de passe oublié ?
            </a>
            <div id="pwd-msg" class="pwd-message">
                ⚠️ La récupération de mot de passe n'est pas disponible sur la version de démonstration.
            </div>
        </div>

        {{-- Bouton --}}
        <button type="submit" class="btn-login">
            Se connecter
        </button>

        {{-- Séparateur --}}
        <div class="separator">
            <hr><span>ou</span><hr>
        </div>

        {{-- Inscription --}}
        <div class="signup-box">
            Pas encore de compte ?
            <a href="{{ route('register') }}">S'inscrire</a>
        </div>

    </form>
</div>

<div class="auth-footer">
    © 2026 MaFilmo
    <a href="#">Conditions</a>
    <a href="#">Confidentialité</a>
</div>

@endsection
