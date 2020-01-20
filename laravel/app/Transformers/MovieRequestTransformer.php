<?php

namespace App\Transformers;

use Illuminate\Http\Request;
use League\Fractal;
use App\Helpers\Date;

class MovieRequestTransformer extends Fractal\TransformerAbstract
{
    public function transform(Request $request)
    {   	
        return [
            'identifier' => $request->identifier,
            'title' => $request->title,
            'year' => $request->year,
            'duration' => $request->duration,
            'director' => $request->director
        ];
    }
}