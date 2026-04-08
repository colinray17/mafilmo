@extends('layouts.mafilmo')
@section('title', 'Rechercher un film')

@section('content')

{{-- Header --}}
<x-header />

{{-- Contenu --}}
<div class="main-container">

    {{-- Titre + barre de recherche --}}
    <div class="search-section">
        <h1>🔍 Rechercher un film</h1>

        <form method="GET" action="{{ route('search') }}" id="search-form">
            <div class="d-flex gap-2 justify-content-center" style="max-width:600px; margin:0 auto;">
                <div style="position:relative; flex:1;">
                    <input
                        type="text"
                        name="q"
                        id="search-input"
                        class="search-input"
                        style="width:100%; padding-right:40px;"
                        placeholder="Titre du film..."
                        value="{{ $query }}"
                        autofocus
                    >
                    <button type="button" id="clear-search"
                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                            background:none; border:none; cursor:pointer; color:#9CA3AF;
                            font-size:18px; line-height:1; display:{{ $query ? 'block' : 'none' }};"
                        onclick="clearSearch()">✕</button>
                </div>
                <button type="submit" class="btn-search">Rechercher</button>
            </div>
        </form>
    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    {{-- Compteur de résultats --}}
    <div id="results-count" class="results-count mt-3"></div>

    {{-- Conteneur des résultats AJAX --}}
    <div id="search-results">
        {{-- Résultats --}}
        @if($query && count($results) > 0)

            <div class="results-count">
                <strong>{{ count($results) }} résultats</strong> pour "{{ $query }}"
            </div>

            <div class="row g-3">
                @foreach($results as $movie)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="movie-card">

                            {{-- Poster + infos cliquables --}}
                            <a href="{{ route('movies.tmdb', $movie['tmdb_id']) }}"
                                style="text-decoration:none;">

                                @if($movie['poster_path'])
                                    <img
                                        src="https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }}"
                                        alt="{{ $movie['title'] }}"
                                        class="movie-poster"
                                    >
                                @else
                                    <div class="movie-poster-placeholder">🎬</div>
                                @endif

                                <div class="movie-info">
                                    <div class="movie-title">{{ $movie['title'] }}</div>
                                    <div class="movie-year">{{ $movie['release_year'] ?? 'Année inconnue' }}</div>
                                </div>
                            </a>

                            {{-- Boutons hors du lien --}}
                            <div class="movie-actions" style="padding: 0 16px 16px;">
                                <button onclick="window.location.href='{{ route('movies.tmdb', $movie['tmdb_id']) }}?back={{ urlencode(url()->current() . '?q=' . $query) }}'"
                                    class="btn-action btn-detail">🔍 Détails</button>
                                <button onclick="addMovie(event, {{ $movie['tmdb_id'] }}, 'seen')"
                                    class="btn-action btn-seen">✅ Vu</button>
                                <button onclick="addMovie(event, {{ $movie['tmdb_id'] }}, 'watchlist')"
                                    class="btn-action btn-watchlist">📌 À voir</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

    @elseif($query && count($results) === 0)
        <div class="empty-state">
            <div class="icon">🎬</div>
            <p>Aucun film trouvé pour "{{ $query }}"</p>
        </div>

    @else
        <div class="empty-state">
            <div class="icon">🎬</div>
            <p>Recherchez un film par son titre</p>
        </div>
    @endif

</div>

@endsection
