<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Date;
use Carbon\Carbon;

class ViewMovieRank extends Model
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
    protected $table = 'view_movie_ranks';

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
     * Override methods save to fix update
     */
    public function save(array $options = []) {
        return null;
    }    

}