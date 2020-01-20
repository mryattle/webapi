<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMovieModule extends Migration{    
    
    /**     
     * Run the migrations.     
     *     
     * @return void     
     */    
    public function up()    {
                    
        
        Schema::create('movie', function (Blueprint $table) {            
            $table->increments('id');
            $table->string('identifier')->nullable(false)->comment('Identifier');
            $table->string('title')->nullable(false)->comment('Title');
            $table->integer('year')->nullable(false)->comment('Year');
            $table->string('duration')->nullable(false)->comment('Duration');
            $table->string('director')->nullable(true)->comment('Director');
            $table->timestampTz('created_at')->nullable(false)->useCurrent();
            $table->timestampTz('updated_at')->nullable(true);
            $table->softDeletesTz();
            
            // Unique
            $table->unique(['identifier']);
            // Foreign keys                  
        });

           

    }

    /**     
     * Reverse the migrations.     
     *     
     * @return void     
     */    
    public function down()    {     
        Schema::dropIfExists('movie'); 
    }
    
}