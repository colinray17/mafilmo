<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Statistiques
        $seenCount      = $user->seenMovies()->count();
        $watchlistCount = $user->watchlist()->count();
        $avgRating      = round($user->averageRating() ?? 0, 1);
        $favoriteGenre  = $user->favoriteGenre();
        $genreStats     = $user->genreStats();
        $ratingStats    = $user->ratingStats();

        // 4 derniers films vus
        $recentMovies = $user->seenMovies()
                             ->orderByPivot('created_at', 'desc')
                             ->take(4)
                             ->get();

        return view('dashboard', compact(
            'user',
            'seenCount',
            'watchlistCount',
            'avgRating',
            'favoriteGenre',
            'recentMovies',
            'genreStats',
            'ratingStats'
        ));
    }
}