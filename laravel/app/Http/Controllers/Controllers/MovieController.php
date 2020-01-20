<?php

namespace App\Http\Controllers\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Models\FileUpload;
use App\Models\Movie; 

// Helpers
use App\Helpers\Query;
use App\Helpers\i18n;

// Transform
use App\Transformers\MovieResponseTransformer;
use App\Transformers\MovieRequestTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

// Validation
use Illuminate\Support\Facades\Validator;


// RPC
use App\Models\MovieLike; 
use App\Transformers\MovieLikeRequestTransformer;
use App\Models\MovieRank; 
use App\Transformers\MovieRankRequestTransformer;
use App\Models\MovieBest; 
use App\Transformers\MovieBestResponseTransformer;

// Events
use App\Events\MessageSent;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        // Messages localization
        \App::setLocale(i18n::locale($request));

        // Query model
        $query = Movie::query();

        // Limit and offset
        if($request->has('offset'))
            $query = $query->skip( (int) $request->input('offset') );

        if($request->has('limit'))
            $query = $query->take( (int) $request->input('limit') );
         
        // Apply filters
        $columns = array(
            "identifier" => array("columname" => "identifier",
                "type" => "string"),
            "title" => array("columname" => "title",
                "type" => "string"),
            "year" => array("columname" => "year",
                "type" => "integer"),
            "duration" => array("columname" => "duration",
                "type" => "string"),
            "director" => array("columname" => "director",
                "type" => "string")
        );

        // Order by
        if($request->has('field') && $request->has('order') && 
            !empty($request->input('order'))) {
            $query = $query->orderBy($columns[$request->input('field')]["columname"], 
                $request->input('order'));
        }        
        
        $query = Query::filter($query, $request, 
            $columns);

        // Relations: *-to-Many 

        return (new Manager())
            ->createData(
                new Collection($query->get(), 
                    new MovieResponseTransformer))->toArray()["data"];
    }

    public function count(Request $request) {
        // Apply filters
        $columns = array(
            "identifier" => array("columname" => "identifier",
                "type" => "string"),
            "title" => array("columname" => "title",
                "type" => "string"),
            "year" => array("columname" => "year",
                "type" => "integer"),
            "duration" => array("columname" => "duration",
                "type" => "string"),
            "director" => array("columname" => "director",
                "type" => "string")
        );
        $query = Query::filter(Movie::query(), 
            $request, $columns);
        // Relations: *-to-Many           
        return $query->count();
    }

    public function view(Request $request, $id) {
        // Messages localization
        \App::setLocale(i18n::locale($request));
        // Check if object exists
        $movie = Movie::findByUnique($id);
        if(is_null($movie))
            return response()->json(['message' => 'Not Found!'], 404); 

        return (new Manager())
            ->createData(
                new Item($movie, 
                    new MovieResponseTransformer))->toArray()["data"];            
    }

    public function create(Request $request) {
        // Messages localization
        \App::setLocale(i18n::locale($request));

        // Validation for required fields and type
        // rules: https://laravel.com/docs/5.8/validation#available-validation-rules
        // Update does not check for unique
        $validator = Validator::make($request->all(), [
            'identifier' => 'string|required|unique:movie,identifier',
            'title' => 'string|required',
            'year' => 'integer|required',
            'duration' => 'string|required',
            'director' => 'string|nullable'
        ]);
        if ($validator->fails()) {
            return response()->json(
                $validator->messages()->all(), 400);
        }

        $data = (new Manager())
            ->createData(
                new Item($request, 
                    new MovieRequestTransformer))->toArray()["data"];

        // Writing in transactional enviroment
        $movie = new Movie($data);

        // Writing in transactional enviroment
        try {
            DB::transaction(function () use ($request, $movie) {
    
                // Relations: One-to-Many 
                
                // Create object
                $movie->save();

                // Relations: Many-to-Many 
            });
        }
        catch (QueryException $e) {
            if(strlen($e->getCode()) === 5 
                && substr($e->getCode(), 0, 2) === '23') {
                abort(409, $e->getMessage());
                exit;
            }
            throw $e;
        }
        return response()->json(
            $this->view($request, $movie->identifier), 
            201);
    }

    public function update(Request $request, $id) {
        // Messages localization
        \App::setLocale(i18n::locale($request));

        // Validation for required fields and type
        // rules: https://laravel.com/docs/5.8/validation#available-validation-rules
        // Update does not check for unique
        $validator = Validator::make($request->all(), [
            'identifier' => 'string|required',
            'title' => 'string|required',
            'year' => 'integer|required',
            'duration' => 'string|required',
            'director' => 'string|nullable'
        ]);
        if ($validator->fails()) {
            return response()->json(
                $validator->messages()->all(), 400);
        }

        // Check if object exists
        $movie = Movie::findByUnique($id);
        if(is_null($movie))
            return response()->json(['message' => 'Not Found!'], 404);  

        $data = (new Manager())
            ->createData(
                new Item($request, 
                    new MovieRequestTransformer))->toArray()["data"];

        // Writing in transactional enviroment   
        $movie->fill($data);

        try {
            // Save new data in the model
            DB::transaction(function () use ($request, $movie, $id) {

                // Relations: One-to-Many 

                // Create object
                $movie->save();

                // Relations: Many-to-Many 
            });
        }
        catch (QueryException $e) {
            if(strlen($e->getCode()) === 5 
                && substr($e->getCode(), 0, 2) === '23') {
                abort(409, $e->getMessage());
                exit;
            }
            throw $e;
        }

        return $this->view($request, $movie->identifier);
    }  

    public function delete_blade_runner(Request $request) {
        // Messages localization
        \App::setLocale(i18n::locale($request));
        
        return response()->json(['message' => 'You shouldn\'t delete Blade Runner'], 500);
    }    

    public function delete(Request $request, $id) {
        // Messages localization
        \App::setLocale(i18n::locale($request));

        // Check if object exists
        $movie = Movie::findByUnique($id);
        if(is_null($movie))
            return response()->json(['message' => 'Not Found!'], 404);

        // Writing in transactional enviroment
        $commited = false;
        DB::transaction(function() use (&$commited, $id) {
            $movie = Movie::findByUnique($id);
            $commited = $movie->delete();
        });
        return (string) $commited;
    }

    // RPC
    public function like(Request $request) {
        // Messages localization
        \App::setLocale(i18n::locale($request));

        // Validation for required fields and type
        // rules: https://laravel.com/docs/5.8/validation#available-validation-rules
        // Update does not check for unique
        $validator = Validator::make($request->all(), [
            'identifier' => 'string|required|exists:movie'
        ]);
        if ($validator->fails()) {
            return response()->json(
                $validator->messages()->all(), 400);
        }

        $data = (new Manager())
            ->createData(
                new Item($request, 
                    new MovieLikeRequestTransformer))->toArray()["data"]; 
                    
                    
        // Writing in transactional enviroment
        $movie_like = new MovieLike($data);    
        
        // Writing in transactional enviroment
        try {        
            $commited = false;
            DB::transaction(function () use (&$commited, $request, $movie_like) {
    
                // Relations: One-to-Many 
                if(!is_null($request->identifier)) {
                    $entity = Movie::findByUnique($request->identifier);
                    $movie_like->movie()->associate($entity);
                }                
                
                // Create object
                $commited = $movie_like->save();
            });
            return (string) $commited;
        }
        catch (QueryException $e) {
            // Conflict user ah already liked
            if(strlen($e->getCode()) === 5 
                && substr($e->getCode(), 0, 2) === '23') {
                // abort(409, $e->getMessage());
                return (string) true;
                exit;
            }
            throw $e;
        }     
    }   
    
    public function rank(Request $request) {
        // Messages localization
        \App::setLocale(i18n::locale($request));

        // Validation for required fields and type
        // rules: https://laravel.com/docs/5.8/validation#available-validation-rules
        // Update does not check for unique
        $validator = Validator::make($request->all(), [
            'identifier' => 'string|required|exists:movie',
            'rank' => 'integer|required|min:0|max:10'
        ]);
        if ($validator->fails()) {
            return response()->json(
                $validator->messages()->all(), 400);
        }         

        $data = (new Manager())
            ->createData(
                new Item($request, 
                    new MovieRankRequestTransformer))->toArray()["data"]; 
         

        // Check if object exists
        $movie_rank = MovieRank::findByMovieAndVisitor(
            $request->identifier,
            \Request::ip());
        if(is_null($movie_rank))
            // Writing in transactional enviroment
            $movie_rank = new MovieRank($data);
        else
            $movie_rank->fill($data);    
        
        // Writing in transactional enviroment
        try {        
            $commited = false;
            DB::transaction(function () use (&$commited, $request, $movie_rank) {
    
                // Relations: One-to-Many 
                if(!is_null($request->identifier)) {
                    $entity = Movie::findByUnique($request->identifier);
                    $movie_rank->movie()->associate($entity);
                }                
                
                // Create object
                $commited = $movie_rank->save();
            });
            return (string) $commited;
        }
        catch (QueryException $e) {
            // Conflict user ah already liked
            if(strlen($e->getCode()) === 5 
                && substr($e->getCode(), 0, 2) === '23') {
                // abort(409, $e->getMessage());
                return (string) true;
                exit;
            }
            throw $e;
        }     
    }     

    public function top10(Request $request)
    {
        // Messages localization
        \App::setLocale(i18n::locale($request));

        // Query model
        $query = MovieBest::query();

        // Relations: *-to-Many 
        $query = $query->orderBy('rank', 'desc')->take(10);

        return (new Manager())
            ->createData(
                new Collection($query->get(), 
                    new MovieBestResponseTransformer))->toArray()["data"];
    }    

}