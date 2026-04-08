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
<div id="genre-modal" style="display:none; position:fixed; inset:0;
    background:rgba(0,0,0,0.5); z-index:9999;
    align-items:center; justify-content:center; backdrop-filter:blur(4px);"
    onclick="if(event.target===this) closeModal('genre-modal')">
    <div style="background:white; border-radius:20px; padding:36px;
        max-width:440px; width:90%; box-shadow:0 25px 50px rgba(0,0,0,0.3);">
        <h3 style="font-family:'Poppins',sans-serif; font-size:20px;
            font-weight:700; color:#1F2937; margin-bottom:24px;">
            🎭 Mes genres préférés
        </h3>
        @forelse($genreStats as $stat)
            <div style="margin-bottom:16px;">
                <div style="display:flex; justify-content:space-between;
                    font-size:14px; font-weight:600; color:#374151; margin-bottom:6px;">
                    <span>{{ $stat['genre'] }}</span>
                    <span>{{ $stat['count'] }} film{{ $stat['count'] > 1 ? 's' : '' }}
                </div>
                <div style="background:#E5E7EB; border-radius:999px; height:8px;">
                    <div style="background:linear-gradient(135deg,#8B5CF6,#6D28D9);
                        height:8px; border-radius:999px; width:{{ $stat['percent'] }}%;
                        transition:width 0.5s ease;"></div>
                </div>
            </div>
        @empty
            <p style="color:#6B7280;">Aucun genre enregistré pour l'instant.</p>
        @endforelse
        <button onclick="closeModal('genre-modal')"
            style="margin-top:24px; width:100%; padding:12px; border:none;
                border-radius:10px; background:#F3F4F6; color:#374151;
                font-weight:700; cursor:pointer; font-size:15px;">
            Fermer
        </button>
    </div>
</div>

{{-- Modal Notes --}}
<div id="rating-modal" style="display:none; position:fixed; inset:0;
    background:rgba(0,0,0,0.5); z-index:9999;
    align-items:center; justify-content:center; backdrop-filter:blur(4px);"
    onclick="if(event.target===this) closeModal('rating-modal')">
    <div style="background:white; border-radius:20px; padding:36px;
        max-width:440px; width:90%; box-shadow:0 25px 50px rgba(0,0,0,0.3);">
        <h3 style="font-family:'Poppins',sans-serif; font-size:20px;
            font-weight:700; color:#1F2937; margin-bottom:24px;">
            ⭐ Répartition de mes notes
        </h3>
        @foreach(array_reverse($ratingStats, true) as $stars => $count)
            <div style="margin-bottom:16px;">
                <div style="display:flex; justify-content:space-between;
                    font-size:14px; font-weight:600; color:#374151; margin-bottom:6px;">
                    <span>{{ str_repeat('★', $stars) }}{{ str_repeat('☆', 5 - $stars) }}</span>
                    <span>{{ $count }} film{{ $count > 1 ? 's' : '' }}</span>
                </div>
                <div style="background:#E5E7EB; border-radius:999px; height:8px;">
                    @php $maxCount = max($ratingStats) ?: 1; @endphp
                    <div style="background:linear-gradient(135deg,#10B981,#059669);
                        height:8px; border-radius:999px;
                        width:{{ $count > 0 ? round(($count / $maxCount) * 100) : 0 }}%;
                        transition:width 0.5s ease;"></div>
                </div>
            </div>
        @endforeach
        <button onclick="closeModal('rating-modal')"
            style="margin-top:24px; width:100%; padding:12px; border:none;
                border-radius:10px; background:#F3F4F6; color:#374151;
                font-weight:700; cursor:pointer; font-size:15px;">
            Fermer
        </button>
    </div>
</div>

@endsection
