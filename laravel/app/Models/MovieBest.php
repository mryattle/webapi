<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\Date;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MovieBest extends Model
{
    // Delte with dates
    // use SoftDeletes;

    /* FIX: Does not work with UTC timestamp with time zone  */
    const UPDATED_AT = null;
    const DELETED_AT = null;
    const CREATED_AT = null;

    /**
     * The properties thhat can be assigned during object initialization.
     *
     * @array string
     */
    protected $fillable = []; 


    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'pgsql';    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'movie_best';

    /**
     * Retrieve element from pkey.
     *
     * @param value  the id of the object
     */
    public static function findById($value)
    {
        return MovieBest::where("id", "=", $value)->get()->first();
    }

    /**
     * Get the movie that owns the like.
     */
    public function movie(): HasOne
    {
        return $this->hasOne(Movie::class, 
            'id', 'movie_id');
    } 

}