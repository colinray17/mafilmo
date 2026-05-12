@extends('layouts.mafilmo')
@section('title', 'Tableau de bord')

@section('content')

{{-- Header --}}
<x-header />

{{-- Contenu principal --}}
<div class="main-container">

    {{-- Bienvenue --}}
    <div class="welcome">
        <h1>Bonjour, {{ $user->name }} 👋</h1>
        <p>Voici un aperçu de votre activité cinéma</p>
    </div>

    {{-- Statistiques --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <a href="{{ route('lists', ['tab' => 'seen']) }}" style="text-decoration:none;">
                <div class="stat-card blue">
                    <div class="stat-header">
                        <div class="stat-icon">🎬</div>
                        <div class="stat-label">Films vus</div>
                    </div>
                    <div class="stat-value">{{ $seenCount }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('lists', ['tab' => 'watchlist']) }}" style="text-decoration:none;">
                <div class="stat-card gold">
                    <div class="stat-header">
                        <div class="stat-icon">📌</div>
                        <div class="stat-label">À voir</div>
                    </div>
                    <div class="stat-value">{{ $watchlistCount }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card purple" onclick="openModal('genre-modal')">
                <div class="stat-header">
                    <div class="stat-icon">🎭</div>
                    <div class="stat-label">Genre préféré</div>
                </div>
                <div class="stat-value genre" title="{{ $favoriteGenre }}">{{ $favoriteGenre }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card green" onclick="openModal('rating-modal')">
                <div class="stat-header">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-label">Note moyenne</div>
                </div>
                <div class="stat-value">
                    {{ $avgRating > 0 ? $avgRating : '—' }}
                    @if($avgRating > 0)<small>/5</small>@endif
                </div>
            </div>
        </div>
    </div>

    {{-- Derniers films --}}
    <div class="section-header">
        <h2>Derniers films ajoutés</h2>
        <a href="{{ route('search') }}" class="btn-primary-custom">+ Rechercher un film</a>
    </div>

    <div class="row g-3">
        @forelse($recentMovies as $movie)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('movies.show', $movie) }}" style="text-decoration:none;">
                    <div class="movie-card dashboard">
                    {{-- Poster --}}
                    @if($movie->poster_path)
                        <img
                            src="https://image.tmdb.org/t/p/w500{{ $movie->poster_path }}"
                            alt="{{ $movie->title }}"
                            class="movie-poster"
                        >
                    @else
                        <div class="movie-poster-placeholder">🎬</div>
                    @endif

                    <div class="movie-info">
                        <div class="movie-title">{{ $movie->title }}</div>
                        <div class="movie-meta">
                            <div class="movie-year">{{ $movie->release_year }}</div>
                            <div class="movie-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star {{ $i <= ($movie->pivot->rating ?? 0) ? '' : 'empty' }}">★</span>
                                @endfor
                            </div>
                        </div>
                    </div>
                    </div>
                </a>
            </div>
        @empty
            {{-- Aucun film encore --}}
            <div class="col-12">
                <div class="empty-state">
                    <div class="empty-icon">🎬</div>
                    <p>Vous n'avez pas encore ajouté de films</p>
                </div>
            </div>
        @endforelse
    </div>


{{-- Modal Genres --}}
<div id="genre-modal" class="modal-overlay">
    <div class="modal-content">
        <h3 class="modal-title">🎭 Mes genres préférés</h3>
        @forelse($genreStats as $stat)
            <div class="modal-stat-item">
                <div class="modal-stat-row">
                    <span>{{ $stat['genre'] }}</span>
                    <span>{{ $stat['count'] }} film{{ $stat['count'] > 1 ? 's' : '' }}</span>
                </div>
                <div class="modal-bar-track">
                    <div class="modal-bar-genre" style="width:{{ $stat['percent'] }}%;"></div>
                </div>
            </div>
        @empty
            <p class="modal-empty">Aucun genre enregistré pour l'instant.</p>
        @endforelse
        <button onclick="closeModal('genre-modal')" class="modal-close-btn">Fermer</button>
    </div>
</div>

{{-- Modal Notes --}}
<div id="rating-modal" class="modal-overlay">
    <div class="modal-content">
        <h3 class="modal-title">⭐ Répartition de mes notes</h3>
        @foreach(array_reverse($ratingStats, true) as $stars => $count)
            <div class="modal-stat-item">
                <div class="modal-stat-row">
                    <span>{{ str_repeat('★', $stars) }}{{ str_repeat('☆', 5 - $stars) }}</span>
                    <span>{{ $count }} film{{ $count > 1 ? 's' : '' }}</span>
                </div>
                <div class="modal-bar-track">
                    @php $maxCount = max($ratingStats) ?: 1; @endphp
                    <div class="modal-bar-rating"
                        style="width:{{ $count > 0 ? round(($count / $maxCount) * 100) : 0 }}%;"></div>
                </div>
            </div>
        @endforeach
        <button onclick="closeModal('rating-modal')" class="modal-close-btn">Fermer</button>
    </div>
</div>

@endsection
