<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\Date;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Movie extends Model
{
    // Delte with dates
    // use SoftDeletes;

    /* FIX: Does not work with UTC timestamp with time zone  */
    const UPDATED_AT = null;

    /**
     * The properties thhat can be assigned during object initialization.
     *
     * @array string
     */
    protected $fillable = ['identifier','title','year','duration','director']; 


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
    protected $table = 'movie';

    /**
     * Retrieve element from pkey.
     *
     * @param value  the id of the object
     */
    public static function findById($value)
    {
        return Movie::where("id", "=", $value)->get()->first();
    }

    /**
     * Retrieve element from unique.
     *
     * @param value the unique value(s) of the object
     */                
    public static function findByUnique($identifier)
    {
        $query = Movie::query();
        $query = $query->where("identifier", "=", $identifier);        
        return $query->get()->first();
    }   

    /**
     * Get the movie that owns the like.
     */
    public function rank(): HasOne
    {
        return $this->hasOne(MovieBest::class, 
            'movie_id', 'id');
    }     
    
    /**
     * Override methods save to fix update
     */
    public function save(array $options = []) {
        // FIX: updated at does not works with timestampz
        if ($this->exists) {
            $this->updated_at = Date::W3c(Carbon::now());
        }
        return parent::save($options);
    }

}