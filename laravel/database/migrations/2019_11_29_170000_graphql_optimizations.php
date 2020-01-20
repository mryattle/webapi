<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class GraphqlOptimizations extends Migration{    
    
    /**     
     * Run the migrations.     
     *     
     * @return void     
     */    
    public function up()    {
                    

        DB::unprepared('
        CREATE VIEW view_movie_ranks AS 
        SELECT movie.*, movie_best.rank  FROM movie
        LEFT JOIN movie_best ON movie_best.movie_id = movie.id;      
        ');
         

    }

    /**     
     * Reverse the migrations.     
     *     
     * @return void     
     */    
    public function down()    {     
        DB::unprepared('
        DROP VIEW view_movie_ranks;');            
    }
    
}