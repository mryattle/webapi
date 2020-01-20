<?php

namespace App\Transformers;

use App\Models\MovieBest;
use League\Fractal;
use App\Helpers\Date;

class MovieBestResponseTransformer extends Fractal\TransformerAbstract
{
    public function transform(MovieBest $movie_best)
    {    
        return [
            'title' => $movie_best->movie->title,
            'rank' => $movie_best->rank
        ];
    }
}