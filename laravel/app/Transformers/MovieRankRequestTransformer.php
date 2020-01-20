<?php

namespace App\Transformers;

use Illuminate\Http\Request;
use League\Fractal;
use App\Helpers\Date;

class MovieRankRequestTransformer extends Fractal\TransformerAbstract
{
    public function transform(Request $request)
    {   	
        return [
            'rank' => $request->rank
        ];
    }
}