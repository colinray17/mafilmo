@extends('layouts.mafilmo')
@section('title', 'Tableau de bord')

@push('styles')
<style>
    /* Welcome */
    .welcome { margin-bottom: 16px; }
    .welcome h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 32px;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 8px;
    }
    .welcome p { font-size: 16px; color: #6B7280; }

    /* Stats */
    .stat-card {
    padding: 12px 16px;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
    overflow: hidden;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px rgba(0,0,0,0.15);
}
.stat-card.blue   { background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%); }
.stat-card.gold   { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); }
.stat-card.purple { background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%); }
.stat-card.green  { background: linear-gradient(135deg, #10B981 0%, #059669 100%); }

/* Icône + label sur la même ligne */
.stat-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 8px;
}
.stat-icon {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex; align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.stat-label {
    font-size: 14px;
    font-weight: 700;
    color: rgba(255,255,255,0.8);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.stat-value {
    font-family: 'Poppins', sans-serif;
    font-size: 28px;
    font-weight: 700;
    color: white;
    line-height: 1;
}
.stat-value small { font-size: 13px; opacity: 0.6; margin-left: 4px; }
.stat-value.genre {
    width:100%;
    font-size: 28px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

    /* Section header */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .section-header h2 {
        font-family: 'Poppins', sans-serif;
        font-size: 24px;
        font-weight: 600;
        color: #1F2937;
    }

    /* Movies grid */
    .movie-info { padding: 16px; }
    .movie-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .movie-rating { display: flex; gap: 2px; font-size: 14px; }

    /* Surcharge posters dashboard uniquement */
    .movie-poster {
        height: 380px;
        aspect-ratio: unset;
        object-fit: cover;
        object-position: top;
    }
    .movie-poster-placeholder {
        height: 380px;
        aspect-ratio: unset;
        font-size: 32px;
    }

    /* Mobile */
    @media (max-width: 576px) {
        .stat-value.genre {
            font-size: 22px;
        }
    }

</style>
@endpush

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
