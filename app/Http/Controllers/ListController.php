<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListController extends Controller
{
    // Page principale — deux onglets
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab  = $request->get('tab', 'seen');

        // Filtres
        $genre  = $request->get('genre');
        $rating = $request->get('rating');
        $year   = $request->get('year');
        $sort   = $request->get('sort', 'recent');

        // Query de base
        $activeQuery = $tab === 'seen' ? $user->seenMovies() : $user->watchlist();

        // Appliquer les filtres
        if ($genre) {
            $activeQuery->where('movies.genre', $genre);
        }
        if ($rating && $tab === 'seen') {
            $activeQuery->wherePivot('rating', $rating);
        }
        if ($year) {
            $activeQuery->where('movies.release_year', $year);
        }

        // Tri
        match($sort) {
            'alpha'  => $activeQuery->orderBy('movies.title', 'asc'),
            'year'   => $activeQuery->orderBy('movies.release_year', 'desc'),
            'rating' => $activeQuery->orderByPivot('rating', 'desc'),
            default  => $activeQuery->orderByPivot('created_at', 'desc'),
        };

        $movies = $activeQuery->get();

        // Données pour les filtres
        $allMovies      = $tab === 'seen' ? $user->seenMovies()->get() : $user->watchlist()->get();
        $genres         = $allMovies->pluck('genre')->filter()->unique()->sort()->values();
        $years          = $allMovies->pluck('release_year')->filter()->unique()->sortDesc()->values();
        $seenCount      = $user->seenMovies()->count();
        $watchlistCount = $user->watchlist()->count();

        return view('lists', compact(
            'movies', 'tab',
            'seenCount', 'watchlistCount',
            'genres', 'years',
            'genre', 'rating', 'year', 'sort'
        ));
    }

    // Supprimer un film de la liste
    public function remove(Movie $movie)
    {
        Auth::user()->movies()->detach($movie->id);
        return back()->with('success', "'{$movie->title}' supprimé de vos listes ✅");
    }

    // Déplacer un film (vu ↔ à voir)
    public function move(Movie $movie)
    {
        $user          = Auth::user();
        $currentStatus = $user->movies()
                              ->where('movie_id', $movie->id)
                              ->first()
                              ?->pivot->status;

        $newStatus = $currentStatus === 'seen' ? 'watchlist' : 'seen';

        $user->movies()->updateExistingPivot($movie->id, [
            'status' => $newStatus
        ]);

        $message = $newStatus === 'seen'
            ? "'{$movie->title}' déplacé vers Films vus ✅"
            : "'{$movie->title}' déplacé vers Liste à voir 📌";

        return back()->with('success', $message);
    }

    // Noter un film
    public function rate(Request $request, Movie $movie)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        Auth::user()->movies()->updateExistingPivot($movie->id, [
            'rating' => $request->rating
        ]);

        return back()->with('success', "Note enregistrée ✅");
    }

    // Commenter un film
    public function comment(Request $request, Movie $movie)
    {
        $request->validate([
            'comment' => 'nullable|string|max:1000',
        ]);

        Auth::user()->movies()->updateExistingPivot($movie->id, [
            'comment' => $request->comment
        ]);

        return back()->with('success', 'Commentaire enregistré ✅');
    }
}