<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RpcActions extends Migration{    
    
    /**     
     * Run the migrations.     
     *     
     * @return void     
     */    
    public function up()    {
                    
        
        Schema::create('movie_likes', function (Blueprint $table) {            
            $table->increments('id');            
            $table->tinyInteger('movie_id')->nullable(false)->comment('Movie');
            $table->ipAddress('visitor')->nullable(false)->comment('Visitor');

            // Unique
            $table->unique(['movie_id', 'visitor']);
            // Foreign keys           
            $table->foreign('movie_id')->references('id')->on('movie')->onDelete('cascade');       
        });

        Schema::create('movie_ranks', function (Blueprint $table) {            
            $table->increments('id');            
            $table->tinyInteger('movie_id')->nullable(false)->comment('Movie');
            $table->ipAddress('visitor')->nullable(false)->comment('Visitor');
            $table->integer('rank')->nullable(false)->comment('Rank from 0 to 10');

            // Unique
            $table->unique(['movie_id', 'visitor']);
            // Foreign keys           
            $table->foreign('movie_id')->references('id')->on('movie')->onDelete('cascade');       
        });        

        Schema::create('movie_actors', function (Blueprint $table) {            
            $table->increments('id');            
            $table->tinyInteger('movie_id')->nullable(false)->comment('Movie');
            $table->string('actor')->nullable(true)->comment('Actor that performed on the movie ');
            
            // Unique
            $table->unique(['movie_id', 'actor']);
            // Foreign keys           
            $table->foreign('movie_id')->references('id')->on('movie')->onDelete('cascade');       
        });  


        Schema::create('movie_best', function (Blueprint $table) {            
            $table->increments('id');            
            $table->tinyInteger('movie_id')->nullable(false)->comment('Movie');
            $table->decimal('rank', 8, 2)->nullable(false)->comment('Rank from 0 to 10');
            
            // Unique
            $table->unique('movie_id');   
            // Foreign keys           
            $table->foreign('movie_id')->references('id')->on('movie')->onDelete('cascade');    
        });  

        DB::unprepared('
        CREATE OR REPLACE FUNCTION movie_best_append()
        RETURNS trigger AS
        $$
        BEGIN
            INSERT INTO movie_best(movie_id, rank)
            VALUES(NEW.id, 0.0);
            RETURN NEW;
        END;
        $$ LANGUAGE plpgsql;
        CREATE TRIGGER movie_best_append_trigger AFTER INSERT ON movie FOR EACH ROW EXECUTE PROCEDURE movie_best_append();

        CREATE OR REPLACE FUNCTION movie_best_rank_insert()
        RETURNS trigger AS
        $$
        BEGIN
            UPDATE movie_best SET 
                rank = (SELECT AVG (rank) AS grank FROM movie_ranks WHERE movie_id = NEW.movie_id)
            WHERE movie_id = NEW.movie_id;
            RETURN NEW;
        END;
        $$ LANGUAGE plpgsql;  
        CREATE TRIGGER movie_best_rank_insert_trigger AFTER INSERT ON movie_ranks FOR EACH ROW EXECUTE PROCEDURE movie_best_rank_insert();    
        
        CREATE OR REPLACE FUNCTION movie_best_rank_update()
        RETURNS trigger AS
        $$
        BEGIN
            UPDATE movie_best SET 
                rank = (SELECT AVG (rank) AS grank FROM movie_ranks WHERE movie_id = OLD.movie_id)
            WHERE movie_id = OLD.movie_id;
            RETURN OLD;
        END;
        $$ LANGUAGE plpgsql;    
        CREATE TRIGGER movie_best_rank_update_trigger AFTER UPDATE ON movie_ranks FOR EACH ROW EXECUTE PROCEDURE movie_best_rank_update();      
        ');
         

    }

    /**     
     * Reverse the migrations.     
     *     
     * @return void     
     */    
    public function down()    {     
        DB::unprepared('
            DROP TRIGGER movie_best_append_trigger ON movie;
            DROP FUNCTION IF EXISTS movie_best_append();
            DROP TRIGGER movie_best_rank_insert_trigger ON movie_ranks;
            DROP FUNCTION IF EXISTS movie_best_rank_insert();
            DROP TRIGGER movie_best_rank_update_trigger ON movie_ranks;
            DROP FUNCTION IF EXISTS movie_best_rank_update();'); 

        Schema::dropIfExists('movie_best');
        Schema::dropIfExists('movie_likes');
        Schema::dropIfExists('movie_ranks');
        Schema::dropIfExists('movie_actors');            
    }
    
}