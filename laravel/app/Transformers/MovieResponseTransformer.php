<?php

namespace App\Transformers;

use App\Models\Movie;
use League\Fractal;
use App\Helpers\Date;

class MovieResponseTransformer extends Fractal\TransformerAbstract
{
    public function transform(Movie $movie)
    {
        // *-to-many     
        return [
            'identifier' => $movie->identifier,
            'title' => $movie->title,
            'year' => $movie->year,
            'duration' => $movie->duration,
            'director' => $movie->director
        ];
    }
}