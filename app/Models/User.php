<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'user_movies')
                    ->withPivot('status', 'rating', 'comment')
                    ->withTimestamps();
    }

    public function seenMovies()
    {
        return $this->movies()->wherePivot('status', 'seen');
    }

    public function watchlist()
    {
        return $this->movies()->wherePivot('status', 'watchlist');
    }

    public function averageRating()
    {
        return $this->movies()
                    ->wherePivot('status', 'seen')
                    ->wherePivotNotNull('rating')
                    ->avg('user_movies.rating');
    }

    public function favoriteGenre()
    {
        $result = DB::table('movies')
            ->join('user_movies', 'movies.id', '=', 'user_movies.movie_id')
            ->where('user_movies.user_id', $this->id)
            ->where('user_movies.status', 'seen')
            ->whereNotNull('movies.genre')
            ->select('movies.genre', DB::raw('COUNT(*) as total'))
            ->groupBy('movies.genre')
            ->orderByDesc('total')
            ->first();

        return $result?->genre ?? '—';
    }

    public function genreStats()
    {
        $total = $this->seenMovies()->count();
        if ($total === 0) return [];

        return DB::table('movies')
            ->join('user_movies', 'movies.id', '=', 'user_movies.movie_id')
            ->where('user_movies.user_id', $this->id)
            ->where('user_movies.status', 'seen')
            ->whereNotNull('movies.genre')
            ->select('movies.genre', DB::raw('COUNT(*) as total'))
            ->groupBy('movies.genre')
            ->orderByDesc('total')
            ->get()
            ->map(fn($g) => [
                'genre'   => $g->genre,
                'count'   => $g->total,
                'percent' => round(($g->total / $total) * 100),
            ])
            ->toArray();
    }

    public function ratingStats()
    {
        $ratings = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratings[$i] = $this->movies()
                ->wherePivot('status', 'seen')
                ->wherePivot('rating', $i)
                ->count();
        }
        return $ratings;
    }

    public function initials()
    {
        $words = explode(' ', trim($this->name));
        return strtoupper(substr($words[0], 0, 1));
    }
}