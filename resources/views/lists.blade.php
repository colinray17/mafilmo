@extends('layouts.mafilmo')
@section('title', 'Mes listes')

@push('styles')
<style>
    /* Titre */
    .page-title {
        font-family: 'Poppins', sans-serif;
        font-size: 32px;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 20px;
    }

    /* Onglets */
    .tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 12px;
        border-bottom: 2px solid #E5E7EB;
        padding-bottom: 0;
    }
    .tab-btn {
        padding: 12px 28px;
        border: none;
        background: none;
        font-size: 16px;
        font-weight: 600;
        color: #6B7280;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        transition: color 0.2s, border-color 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .tab-btn:hover { color: #3B82F6; }
    .tab-btn.active {
        color: #1E3A8A;
        border-bottom-color: #F59E0B;
    }
    .tab-count {
        background: #E5E7EB;
        color: #6B7280;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 12px;
        margin-left: 6px;
    }
    .tab-btn.active .tab-count {
        background: #1E3A8A;
        color: white;
    }

    /* Grille films */
    .movie-poster-wrap { position: relative; }
    .movie-poster-wrap a { display: block; }
    .movie-poster-wrap a:hover { opacity: 0.9; }

    /* Badge statut */
    .status-badge {
        position: absolute;
        top: 10px; left: 10px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }
    .badge-seen {
        background: #1E3A8A;
        color: white;
    }
    .badge-watchlist {
        background: #F59E0B;
        color: white;
    }

    .movie-info { padding: 16px; }

    .movie-comment {
        font-size: 12px;
        color: #6B7280;
        font-style: italic;
        margin-bottom: 10px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Étoiles notation */
    .star-rating {
        display: flex;
        gap: 4px;
        margin-bottom: 14px;
    }
    .star-btn {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        padding: 0;
        transition: transform 0.1s;
        line-height: 1;
    }
    .star-btn:hover { transform: scale(1.2); }

    /* Actions */
    .btn-move {
        background: #EFF3FB;
        color: #1E3A8A;
    }

    .btn-remove {
        background: #FEE2E2;
        color: #EF4444;
    }

    /* Filtres */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 18px;
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 14px;
        padding: 12px;
    }
    .filter-select {
        height: 40px;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 14px;
        color: #374151;
        background: white;
        cursor: pointer;
    }
    .btn-reset {
        height: 40px;
        padding: 0 16px;
        background: #FEE2E2;
        color: #EF4444;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    #list-search {
        cursor: text;
    }

    .movie-card-hidden {
        display: none;
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<x-header />

<div class="main-container">

    <h1 class="page-title">🎬 Mes listes</h1>

    {{-- Message succès --}}
    @if(session('success'))
        <div class="alert-success-custom">{{ session('success') }}</div>
    @endif

    {{-- Onglets --}}
    <div class="tabs">
        <a href="{{ route('lists', ['tab' => 'seen']) }}"
        class="tab-btn {{ $tab === 'seen' ? 'active' : '' }}">
            ✅ Films vus
            <span class="tab-count">{{ $seenCount }}</span>
        </a>
        <a href="{{ route('lists', ['tab' => 'watchlist']) }}"
        class="tab-btn {{ $tab === 'watchlist' ? 'active' : '' }}">
            📌 À voir
            <span class="tab-count">{{ $watchlistCount }}</span>
        </a>
    </div>

    {{-- Barre de filtres --}}
    <form method="GET" action="{{ route('lists') }}" id="filter-form">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="filter-bar">

            {{-- Genre --}}
            <select name="genre" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                <option value="">🎭 Tous les genres</option>
                @foreach($genres as $g)
                    <option value="{{ $g }}" {{ $genre === $g ? 'selected' : '' }}>{{ $g }}</option>
                @endforeach
            </select>

            {{-- Note (seulement pour Films vus) --}}
            @if($tab === 'seen')
                <select name="rating" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                    <option value="">⭐ Toutes les notes</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ $rating == $i ? 'selected' : '' }}>
                            {{ str_repeat('★', $i) }} {{ $i }} étoile{{ $i > 1 ? 's' : '' }}
                        </option>
                    @endfor
                </select>
            @endif

            {{-- Année --}}
            <select name="year" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                <option value="">📅 Toutes les années</option>
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>

            {{-- Tri --}}
            <select name="sort" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                <option value="recent" {{ $sort === 'recent' ? 'selected' : '' }}>🕐 Plus récents</option>
                <option value="alpha"  {{ $sort === 'alpha'  ? 'selected' : '' }}>🔤 A → Z</option>
                <option value="year"   {{ $sort === 'year'   ? 'selected' : '' }}>📅 Année</option>
                @if($tab === 'seen')
                    <option value="rating" {{ $sort === 'rating' ? 'selected' : '' }}>⭐ Note</option>
                @endif
            </select>

            {{-- Recherche dans la liste --}}
            <input
                type="text"
                id="list-search"
                class="filter-select"
                placeholder="🔎 Rechercher..."
                style="min-width: 160px;">

            {{-- Réinitialiser --}}
            @if($genre || $rating || $year || $sort !== 'recent')
                <a href="{{ route('lists', ['tab' => $tab]) }}" class="btn-reset">
                    ✕ Réinitialiser
                </a>
            @endif

        </div>
    </form>

    {{-- Contenu onglets --}}
    <div class="row g-3">
        @forelse($movies as $movie)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="movie-card">
                    <div class="movie-poster-wrap">
                        <a href="{{ route('movies.show', $movie) }}">
                            @if($movie->poster_path)
                                <img src="https://image.tmdb.org/t/p/w500{{ $movie->poster_path }}"
                                    alt="{{ $movie->title }}" class="movie-poster">
                            @else
                                <div class="movie-poster-placeholder">🎬</div>
                            @endif
                        </a>
                        <span class="status-badge {{ $tab === 'seen' ? 'badge-seen' : 'badge-watchlist' }}">
                            {{ $tab === 'seen' ? '✅ Vu' : '📌 À voir' }}
                        </span>
                    </div>

                    <div class="movie-info">
                        <div class="movie-title">{{ $movie->title }}</div>
                        <div class="movie-year">{{ $movie->release_year ?? '—' }}</div>

                        {{-- Notation étoiles (Films vus uniquement) --}}
                        @if($tab === 'seen')
                            <form method="POST" action="{{ route('movies.rate', $movie) }}">
                                @csrf @method('PATCH')
                                <div class="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="submit" name="rating" value="{{ $i }}"
                                                class="star-btn">
                                            <span class="{{ $i <= ($movie->pivot->rating ?? 0) ? 'star-filled' : 'star-empty' }}">★</span>
                                        </button>
                                    @endfor
                                </div>
                            </form>

                            {{-- Commentaire --}}
                            @if($movie->pivot->comment)
                                <div class="movie-comment">
                                    "{{ $movie->pivot->comment }}"
                                </div>
                            @endif
                        @endif

                        {{-- Actions --}}
                        <div class="movie-actions">
                            {{-- Détails --}}
                            <a href="{{ route('movies.show', $movie) }}" class="btn-action btn-detail">🔍 Détails</a>

                            {{-- Déplacer --}}
                            <form method="POST" action="{{ route('movies.move', $movie) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-action btn-move">
                                    {{ $tab === 'seen' ? '📌 À voir' : '✅ Vu' }}
                                </button>
                            </form>

                            {{-- Supprimer --}}
                            <form method="POST" action="{{ route('movies.remove', $movie) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-remove">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <div class="icon">{{ $tab === 'seen' ? '🎬' : '📌' }}</div>
                    <p>{{ $tab === 'seen' ? 'Vous n\'avez pas encore de films vus' : 'Votre liste à voir est vide' }}</p>
                    <a href="{{ route('search') }}" class="btn-primary-custom">+ Rechercher un film</a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
