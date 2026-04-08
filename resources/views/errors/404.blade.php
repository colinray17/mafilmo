@extends('layouts.mafilmo')
@section('title', 'Page introuvable')
@section('body-class', 'auth-body')

@push('styles')
<style>
    .error-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-height: 60vh;
        position: relative;
        z-index: 10;
        padding: 40px 20px;
    }

    .error-code {
        font-family: 'Poppins', sans-serif;
        font-size: 120px;
        font-weight: 700;
        color: white;
        line-height: 1;
        margin-bottom: 8px;
        opacity: 0.9;
    }

    .error-icon {
        font-size: 64px;
        margin-bottom: 24px;
    }

    .error-title {
        font-family: 'Poppins', sans-serif;
        font-size: 28px;
        font-weight: 700;
        color: white;
        margin-bottom: 12px;
    }

    .error-message {
        font-size: 16px;
        color: rgba(255,255,255,0.7);
        margin-bottom: 40px;
        max-width: 400px;
    }

    .btn-home {
        background: white;
        color: #1E3A8A;
        padding: 14px 32px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.2s;
        display: inline-block;
    }
    .btn-home:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        color: #1E3A8A;
    }

    .btn-back-link {
        display: block;
        margin-top: 16px;
        color: rgba(255,255,255,0.6);
        font-size: 14px;
        text-decoration: none;
        transition: color 0.2s;
    }
    .btn-back-link:hover { color: white; }
</style>
@endpush

@section('content')

{{-- Cercles décoratifs --}}
<div class="bg-circle bg-circle-1"></div>
<div class="bg-circle bg-circle-2"></div>
<div class="bg-circle bg-circle-3"></div>
<div class="bg-circle bg-circle-4"></div>

{{-- Logo --}}
<a href="{{ route('dashboard') }}" class="auth-logo">Ma<span>Filmo</span></a>
<div class="auth-tagline">Votre journal de cinéma personnel</div>

{{-- Contenu erreur --}}
<div class="error-container">
    <div class="error-icon">🎬</div>
    <div class="error-code">404</div>
    <h1 class="error-title">Page introuvable</h1>
    <p class="error-message">
        Cette page n'existe pas ou a été déplacée.
        Retournez sur MaFilmo pour continuer votre aventure cinéma !
    </p>

    @auth
        <a href="{{ route('dashboard') }}" class="btn-home">
            🏠 Retour au tableau de bord
        </a>
    @else
        <a href="{{ route('login') }}" class="btn-home">
            🔑 Se connecter
        </a>
    @endauth

    <a href="javascript:history.back()" class="btn-back-link">
        ← Revenir à la page précédente
    </a>
</div>

@endsection