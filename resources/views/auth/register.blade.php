@extends('layouts.mafilmo')
@section('title', 'Inscription')
@section('body-class', 'auth-body')

@push('styles')
<style>

    .form-card {
        background: white;
        border-radius: 20px;
        padding: 40px 50px 50px 50px;
        width: 100%;
        max-width: 480px;
        box-shadow: 0 20px 25px rgba(0,0,0,0.15);
        position: relative;
        z-index: 10;
    }

    .form-title {
        font-family: 'Poppins', sans-serif;
        font-size: 32px;
        font-weight: 700;
        color: #1F2937;
        text-align: center;
        margin-bottom: 30px;
    }

    .form-label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        display: block;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        height: 52px;
        background: #F9FAFB;
        border: 1px solid #D1D5DB;
        border-radius: 12px;
        padding: 0 16px;
        font-size: 15px;
        color: #1F2937;
        display: block;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: #3B82F6;
        background: white;
        box-shadow: none;
    }

    .form-control::placeholder { color: #9CA3AF; }

    .form-control.is-invalid {
        border-color: #EF4444;
    }

    .form-group {
        margin-bottom: 20px;
    }
    .form-group-last {
        margin-bottom: 28px;
    }

    .invalid-feedback {
        font-size: 12px;
        color: #EF4444;
        margin-top: 6px;
        display: block;
    }

    .btn-register {
        width: 100%;
        height: 56px;
        background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        display: block;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        color: white;
    }

    .separator {
        display: flex;
        align-items: center;
        margin: 24px 0;
    }
    .separator hr {
        flex: 1;
        margin: 0;
        border-color: #E5E7EB;
    }
    .separator span {
        padding: 0 16px;
        font-size: 14px;
        color: #6B7280;
        line-height: 1;
    }

    .login-box {
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 10px;
        padding: 16px;
        text-align: center;
        font-size: 15px;
        color: #6B7280;
    }
    .login-box a {
        color: #3B82F6;
        font-weight: 700;
        text-decoration: none;
    }
    .login-box a:hover { text-decoration: underline; }

    @media (max-width: 576px) {
        .form-card {
            padding: 30px 20px;
            border-radius: 16px;
            margin: 0 16px;
        }
        .form-title { font-size: 26px; }
    }

</style>
@endpush

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
                autocomplete="new-password"
            >
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
