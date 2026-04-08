@extends('layouts.mafilmo')
@section('title', 'Page introuvable')
@section('body-class', 'auth-body')

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
