<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Services\TmdbService;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    public function __construct(private TmdbService $tmdb) {}

    public function show(Movie $movie)
    {
        $details = $this->tmdb->getMovie($movie->tmdb_id);

        if (!$details) {
            return back()->with('error', 'Film introuvable sur TMDB.');
        }

        $userMovie   = Auth::user()->movies()->where('movie_id', $movie->id)->first();
        $userStatus  = $userMovie?->pivot->status;
        $userRating  = $userMovie?->pivot->rating;
        $userComment = $userMovie?->pivot->comment;

        $this->saveBackUrl();

        return view('movie', compact('movie', 'details', 'userStatus', 'userRating', 'userComment'));
    }

    public function showByTmdbId(string $tmdbId)
    {
        $tmdbId  = (int) explode('?', $tmdbId)[0];
        $details = $this->tmdb->getMovie($tmdbId);

        if (!$details) {
            return back()->with('error', 'Film introuvable sur TMDB.');
        }

        $movie       = Movie::where('tmdb_id', $tmdbId)->first();
        $userStatus  = null;
        $userRating  = null;
        $userComment = null;

        if ($movie) {
            $userMovie   = Auth::user()->movies()->where('movie_id', $movie->id)->first();
            $userStatus  = $userMovie?->pivot->status;
            $userRating  = $userMovie?->pivot->rating;
            $userComment = $userMovie?->pivot->comment;
        }

        $this->saveBackUrl();

        return view('movie', compact('movie', 'details', 'userStatus', 'userRating', 'userComment'));
    }

    // Sauvegarder l'URL de retour en session
    private function saveBackUrl(): void
    {
        $backUrl = request()->get('back');
        if ($backUrl) {
            request()->session()->put('movie_back_url', $backUrl);
        } elseif (!request()->session()->has('movie_back_url') ||
            !str_contains(url()->previous(), '/movies/')) {
            request()->session()->put('movie_back_url', url()->previous());
        }
    }
}