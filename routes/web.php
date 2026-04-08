<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Recherche
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/search/ajax', [SearchController::class, 'ajax'])->name('search.ajax');
    Route::post('/movies/add', [SearchController::class, 'addMovie'])->name('movies.add');

    // Films
    Route::get('/movies/tmdb/{tmdbId}', [MovieController::class, 'showByTmdbId'])->name('movies.tmdb');
    Route::get('/movies/{movie}', [MovieController::class, 'show'])->name('movies.show');

    // Listes
    Route::get('/lists', [ListController::class, 'index'])->name('lists');
    Route::delete('/movies/{movie}/remove', [ListController::class, 'remove'])->name('movies.remove');
    Route::patch('/movies/{movie}/move', [ListController::class, 'move'])->name('movies.move');
    Route::patch('/movies/{movie}/rate', [ListController::class, 'rate'])->name('movies.rate');
    Route::patch('/movies/{movie}/comment', [ListController::class, 'comment'])->name('movies.comment');

    // Profil
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Routes d'authentification Breeze
require __DIR__.'/auth.php';
