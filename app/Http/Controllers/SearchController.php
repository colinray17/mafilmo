<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function __construct(private TmdbService $tmdb) {}

    // Page de recherche
    public function index(Request $request)
    {
        $query   = $request->get('q', '');
        $results = [];

        if ($query) {
            $results = $this->tmdb->searchMovies($query);
        }

        return view('search', compact('query', 'results'));
    }

    // Ajouter un film à "Films vus" ou "Liste à voir"
    public function addMovie(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|integer',
            'status'  => 'required|in:seen,watchlist',
        ]);

        $movieData = $this->tmdb->getMovie($request->tmdb_id);

        if (!$movieData) {
            return back()->with('error', 'Film introuvable.');
        }

        $movie = Movie::firstOrCreate(
            ['tmdb_id' => $movieData['tmdb_id']],
            [
                'title'        => $movieData['title'],
                'release_year' => $movieData['release_year'],
                'poster_path'  => $movieData['poster_path'],
                'genre'        => $movieData['genre'],
            ]
        );

        $existing = Auth::user()->movies()
            ->where('movie_id', $movie->id)
            ->wherePivot('status', $request->status)
            ->exists();

        if ($existing) {
            $label   = $request->status === 'seen' ? 'films vus' : 'liste à voir';
            $message = "'{$movie->title}' est déjà dans vos {$label}.";

            if ($request->ajax()) {
                return response()->json([
                    'already_exists' => true,
                    'message'        => $message,
                ]);
            }

            return back()->with('info', $message);
        }

        Auth::user()->movies()->syncWithoutDetaching([
            $movie->id => ['status' => $request->status]
        ]);

        $message = $request->status === 'seen'
            ? "'{$movie->title}' ajouté à vos films vus"
            : "'{$movie->title}' ajouté à votre liste à voir";

        if ($request->ajax()) {
            return response()->json(['message' => $message]);
        }

        $previousUrl = url()->previous();
        if (str_contains($previousUrl, '/movies/')) {
            return redirect($previousUrl)->with('success', $message);
        }

        return redirect()->route('search', ['q' => $request->get('q', '')])->with('success', $message);
    }

    // Recherche AJAX
    public function ajax(Request $request)
    {
      $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = $this->tmdb->searchMovies($query);

        return response()->json(['results' => $results]);
    }
}
