<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'tmdb_id',
        'title',
        'release_year',
        'poster_path',
        'genre',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_movies')
                    ->withPivot('status', 'rating', 'comment')
                    ->withTimestamps();
    }
}