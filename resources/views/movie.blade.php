@extends('layouts.mafilmo')
@section('title', $details['title'])
@php use Carbon\Carbon; @endphp

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
