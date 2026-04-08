@extends('layouts.mafilmo')
@section('title', 'Mon profil')

@push('styles')
<style>
    /* En-tête profil */
    .avatar-large {
        width: 80px; height: 80px;
        background: linear-gradient(135deg, #1E3A8A, #3B82F6);
        border-radius: 50%;
        display: flex; align-items: center;
        justify-content: center;
        color: white; font-weight: 700;
        font-size: 32px;
        font-family: 'Poppins', sans-serif;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(30,58,138,0.3);
    }

    /* Cards */
    .card {
        background: white;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        margin-bottom: 24px;
    }
    .card h2 {
        font-family: 'Poppins', sans-serif;
        font-size: 18px; font-weight: 700;
        color: #1F2937; margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #F5F4F0;
    }

    /* Formulaire */
    .form-group { margin-bottom: 20px; }
    .form-group label {
        display: block;
        font-size: 14px; font-weight: 600;
        color: #374151; margin-bottom: 8px;
    }
    .form-group input {
        width: 100%; height: 52px;
        background: #F9FAFB;
        border: 1px solid #D1D5DB;
        border-radius: 12px;
        padding: 0 16px;
        font-size: 15px; color: #1F2937;
        box-sizing: border-box;
        transition: border-color 0.3s, background 0.3s;
    }
    .form-group input:focus {
        outline: none;
        border-color: #3B82F6;
        background: white;
    }
    .form-group input::placeholder { color: #9CA3AF; }
    .invalid-feedback {
        font-size: 12px; color: #EF4444;
        margin-top: 6px; display: block;
    }
    .form-group input.is-invalid { border-color: #EF4444; }

    .profile-name {
        font-family: 'Poppins', sans-serif;
        font-size: 28px;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 4px;
    }
    .profile-email {
        font-size: 15px;
        color: #6B7280;
        margin: 0;
    }
    .profile-container {
        max-width: 680px;
    }

    /* Bouton */
    .btn-save {
        width: 100%; height: 52px;
        background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 100%);
        color: white; border: none;
        border-radius: 12px;
        font-size: 15px; font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        margin-top: 8px;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59,130,246,0.3);
    }

    /* Séparateur mot de passe */
    .password-hint {
        font-size: 12px; color: #9CA3AF;
        margin-top: 6px; display: block;
    }

    @media (max-width: 768px) {
        .avatar-large { width: 60px; height: 60px; font-size: 24px; }
    }

</style>
@endpush

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
                <span class="password-hint">Minimum 8 caractères</span>
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
