@extends('layouts.mafilmo')
@section('title', 'Mes listes')

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
                        <div class="movie-meta">
                            <div class="movie-year">{{ $movie->release_year ?? '—' }}</div>
                            @if($tab === 'seen')
                                <form method="POST" action="{{ route('movies.rate', $movie) }}">
                                    @csrf @method('PATCH')
                                    <div class="star-rating" style="margin-bottom:0;">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="submit" name="rating" value="{{ $i }}"
                                                    class="star-btn">
                                                <span class="{{ $i <= ($movie->pivot->rating ?? 0) ? 'star-filled' : 'star-empty' }}">★</span>
                                            </button>
                                        @endfor
                                    </div>
                                </form>
                            @endif
                        </div>

                        {{-- Commentaire --}}
                        @if($tab === 'seen' && $movie->pivot->comment)
                            <div class="movie-comment">
                                "{{ $movie->pivot->comment }}"
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="movie-actions">
                            <a href="{{ route('movies.show', $movie) }}" class="btn-action btn-detail">🔍 Détails</a>

                            <form method="POST" action="{{ route('movies.move', $movie) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-action btn-move">
                                    {{ $tab === 'seen' ? '📌 À voir' : '✅ Vu' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('movies.remove', $movie) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-remove">🗑️</button>
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
