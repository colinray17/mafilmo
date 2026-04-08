@extends('layouts.mafilmo')
@section('title', $details['title'])
@php use Carbon\Carbon; @endphp

@push('styles')
<style>

    /* Backdrop */
    .backdrop {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        filter: brightness(0.5);
        position: absolute;
        top: 0;
        left: 0;
        z-index: 0;
    }
    .backdrop-wrap {
        position: relative;
        height: 400px;
        overflow: hidden;
        background: #1a1a2e;
    }
    .backdrop-placeholder {
        width: 100%; height: 400px;
        background: linear-gradient(135deg, #1E3A8A, #3B82F6);
    }

    /* Hero section */
    .hero {
        position: absolute;
        bottom: 0;
        left: 0; right: 0;
        z-index: 2;
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 20px 30px 20px;
        display: flex;
        gap: 40px;
        align-items: flex-end;
    }

    .poster-wrap {
        flex-shrink: 0;
        width: 220px;
    }
    .poster-wrap img {
        width: 100%;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        display: block;
    }
    .poster-placeholder {
        width: 100%;
        aspect-ratio: 2/3;
        background: #2d2d4e;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 56px;
    }

    .hero-info { padding-bottom: 20px; }
    .hero-title {
        font-family: 'Poppins', sans-serif;
        font-size: 40px; font-weight: 700;
        color: white;
        text-shadow: 0 2px 8px rgba(0,0,0,0.5);
        margin-bottom: 8px;
        line-height: 1.2;
    }
    .hero-meta {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .hero-meta span {
        color: rgba(255,255,255,0.8);
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .tmdb-score {
        background: #F59E0B;
        color: #1a1a2e;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 14px;
    }

    /* Genres */
    .genre-tags {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .genre-tag {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 13px;
        backdrop-filter: blur(4px);
    }

    /* Contenu principal */
    .main-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    /* Sections */
    .section {
        background: white;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        margin-bottom: 24px;
    }
    .section h2 {
        font-family: 'Poppins', sans-serif;
        font-size: 18px; font-weight: 700;
        color: #1F2937;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 2px solid #F5F4F0;
    }
    .section p {
        color: #4B5563;
        line-height: 1.7;
        font-size: 15px;
    }

    /* Info liste */
    .info-list { list-style: none; }
    .info-list li {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #F3F4F6;
        font-size: 14px;
    }
    .info-list li:last-child { border-bottom: none; }
    .info-label { color: #6B7280; font-weight: 500; }
    .info-value { color: #1F2937; font-weight: 600; }

    /* Casting */
    .cast-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .cast-card {
        background: #F5F4F0;
        border-radius: 10px;
        padding: 10px 16px;
    }
    .cast-name {
        font-size: 14px;
        font-weight: 700;
        color: #1F2937;
    }
    .cast-character {
        font-size: 12px;
        color: #6B7280;
        margin-top: 2px;
    }

    /* Sidebar — Ma note */
    .sidebar .section { margin-bottom: 20px; }

    .star-rating-large {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin: 16px 0;
    }
    .star-btn-large {
        background: none; border: none;
        font-size: 32px; cursor: pointer;
        padding: 0; transition: transform 0.1s;
        line-height: 1;
    }

    .rating-label {
        text-align: center;
        font-size: 13px;
        color: #6B7280;
        margin-bottom: 16px;
    }

    /* Critique personnelle */
    .comment-display {
        background: #F9FAFB;
        border-left: 3px solid #3B82F6;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 14px;
        color: #4B5563;
        font-style: italic;
        line-height: 1.6;
    }
    .comment-textarea {
        width: 100%;
        border: 1px solid #D1D5DB;
        border-radius: 12px;
        padding: 12px;
        font-size: 14px;
        color: #1F2937;
        background: #F9FAFB;
        resize: vertical;
        font-family: 'Inter', sans-serif;
        box-sizing: border-box;
    }
    .btn-save {
        width: 100%;
        margin-top: 10px;
        padding: 12px;
        background: linear-gradient(135deg, #1E3A8A, #3B82F6);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: transform 0.2s, opacity 0.2s;
    }
    .btn-save:hover { transform: translateY(-2px); opacity: 0.9; }

    /* Boutons statut */
    .status-buttons {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .btn-status {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s, opacity 0.2s;
    }
    .btn-status:hover { transform: translateY(-2px); opacity: 0.9; }

    .btn-remove-list {
        background: #FEE2E2;
        color: #EF4444;
    }
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #6B7280;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 24px;
        transition: color 0.2s;
    }
    .btn-back:hover { color: #1E3A8A; }

    .current-status {
        text-align: center;
        padding: 10px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 12px;
    }
    .status-seen      { background: #EFF3FB; color: #1E3A8A; }
    .status-watchlist { background: #FEF3C7; color: #92400E; }
    .status-none      { background: #F3F4F6; color: #6B7280; }

    @media (max-width: 768px) {
        .hero { flex-direction: column; }
        .poster-wrap { width: 140px; }
        .hero-title { font-size: 24px; }
        .backdrop-wrap { height: 280px; }
        .backdrop { height: 280px; }
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<x-header />

{{-- Backdrop --}}
<div class="backdrop-wrap">
    @if($details['backdrop_path'])
        <img src="https://image.tmdb.org/t/p/w1280{{ $details['backdrop_path'] }}"
             class="backdrop" alt="">
    @else
        <div class="backdrop-placeholder"></div>
    @endif

    {{-- Hero superposé sur le backdrop --}}
    <div class="hero d-flex gap-4 align-items-end">
        <div class="poster-wrap">
            @if($details['poster_path'])
                <img src="https://image.tmdb.org/t/p/w500{{ $details['poster_path'] }}"
                     alt="{{ $details['title'] }}">
            @else
                <div class="poster-placeholder">🎬</div>
            @endif
        </div>

        <div class="hero-info flex-grow-1">
            <h1 class="hero-title">{{ $details['title'] }}</h1>

            <div class="hero-meta">
                @if($details['release_year'])
                    <span>📅 {{ $details['release_year'] }}</span>
                @endif
                @if($details['runtime'])
                    <span>⏱️ {{ floor($details['runtime'] / 60) }}h{{ $details['runtime'] % 60 }}min</span>
                @endif
                @if($details['vote_average'])
                    <span>⭐ <span class="tmdb-score">{{ $details['vote_average'] }}/10</span> TMDB</span>
                @endif
                @if($details['director'])
                    <span>🎬 {{ $details['director'] }}</span>
                @endif
            </div>

            <div class="genre-tags">
                @foreach($details['genres'] as $genre)
                    <span class="genre-tag">{{ $genre }}</span>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Contenu --}}
<div class="main-container">

    {{-- Lien retour --}}
    <a href="{{ session('movie_back_url', url()->previous()) }}" class="btn-back">← Retour</a>

    @if(session('success'))
        <div class="alert-success-custom">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-8">

            {{-- Synopsis --}}
            @if($details['overview'])
                <div class="section">
                    <h2>📖 Synopsis</h2>
                    <p>{{ $details['overview'] }}</p>
                </div>
            @endif

            {{-- Casting --}}
            @if(count($details['cast']) > 0)
                <div class="section">
                    <h2>🎭 Casting principal</h2>
                    <div class="cast-grid">
                        @foreach($details['cast'] as $actor)
                            <div class="cast-card">
                                <div class="cast-name">{{ $actor['name'] }}</div>
                                <div class="cast-character">{{ $actor['character'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Infos techniques --}}
            <div class="section">
                <h2>ℹ️ Informations</h2>
                <ul class="info-list">
                    @if($details['release_date'])
                        <li>
                            <span class="info-label">Date de sortie</span>
                            <span class="info-value">{{ Carbon::parse($details['release_date'])->format('d/m/Y') }}</span>
                        </li>
                    @endif
                    @if($details['runtime'])
                        <li>
                            <span class="info-label">Durée</span>
                            <span class="info-value">{{ floor($details['runtime'] / 60) }}h {{ $details['runtime'] % 60 }}min</span>
                        </li>
                    @endif
                    @if($details['director'])
                        <li>
                            <span class="info-label">Réalisateur</span>
                            <span class="info-value">{{ $details['director'] }}</span>
                        </li>
                    @endif
                    @if(count($details['genres']) > 0)
                        <li>
                            <span class="info-label">Genres</span>
                            <span class="info-value">{{ implode(', ', $details['genres']) }}</span>
                        </li>
                    @endif
                    <li>
                        <span class="info-label">Note TMDB</span>
                        <span class="info-value">⭐ {{ $details['vote_average'] }}/10</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-12 col-lg-4">

            {{-- Ma note --}}
            <div class="section">
                <h2>⭐ Ma note</h2>

                {{-- Statut actuel --}}
                @if($userStatus === 'seen')
                    <div class="current-status status-seen">✅ Dans mes films vus</div>
                @elseif($userStatus === 'watchlist')
                    <div class="current-status status-watchlist">📌 Dans ma liste à voir</div>
                @else
                    <div class="current-status status-none">Non ajouté à vos listes</div>
                @endif

                {{-- Étoiles --}}
                @if($userStatus === 'seen')
                    <form method="POST" action="{{ route('movies.rate', $movie) }}">
                        @csrf @method('PATCH')
                        <div class="star-rating-large">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="submit" name="rating" value="{{ $i }}"
                                        class="star-btn-large">
                                    <span class="{{ $i <= ($userRating ?? 0) ? 'star-filled' : 'star-empty' }}">★</span>
                                </button>
                            @endfor
                        </div>
                        <div class="rating-label">
                            {{ $userRating ? "Votre note : {$userRating}/5" : "Cliquez pour noter" }}
                        </div>
                    </form>
                @endif

                {{-- Commentaire personnel --}}
                @if($userStatus === 'seen' && $movie)
                    <div class="section mt-3">
                        <h2>📝 Ma critique</h2>

                        {{-- Affichage du commentaire existant --}}
                        @if($userComment ?? false)
                            <div class="comment-display">
                                "{{ $userComment }}"
                            </div>
                        @endif

                        <form method="POST" action="{{ route('movies.comment', $movie) }}">
                            @csrf @method('PATCH')
                            <textarea
                                name="comment"
                                rows="4"
                                placeholder="Votre critique personnelle..."
                                class="comment-textarea">{{ $userComment ?? '' }}</textarea>
                            <button type="submit"
                                    class="btn-save">
                                💾 Enregistrer
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="status-buttons">
                    @if($userStatus !== 'seen')
                        <form method="POST" action="{{ route('movies.add') }}">
                            @csrf
                            <input type="hidden" name="tmdb_id" value="{{ $movie?->tmdb_id ?? $details['tmdb_id'] }}">
                            <input type="hidden" name="status" value="seen">
                            <button type="submit" class="btn-status btn-seen">✅ Marquer comme vu</button>
                        </form>
                    @endif

                    @if($userStatus !== 'watchlist')
                        <form method="POST" action="{{ route('movies.add') }}">
                            @csrf
                            <input type="hidden" name="tmdb_id" value="{{ $movie?->tmdb_id ?? $details['tmdb_id'] }}">
                            <input type="hidden" name="status" value="watchlist">
                            <button type="submit" class="btn-status btn-watchlist">📌 Ajouter à ma liste</button>
                        </form>
                    @endif

                    @if($movie && $userStatus)
                        <form method="POST" action="{{ route('movies.remove', $movie) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-status btn-remove-list">
                                🗑️ Retirer de mes listes
                            </button>
                        </form>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

@endsection
