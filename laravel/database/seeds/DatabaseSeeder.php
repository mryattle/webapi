<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Models\Movie;
use App\Models\MovieRank;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(MoviesTableSeeder::class);
        User::query()->truncate(); // truncate user table each time of seeders run
        User::create([ // create a new user
            'email' => 'test@laravel',
            'password' => Hash::make('12345'),
            'name' => 'Test'
        ]);  
        
        Movie::query()->truncate(); // truncate user table each time of seeders run
        Movie::create(['identifier' => 'blade-runner', 'title' => 'Blade Runner', 
            'year' => 1982, 'director' => 'Ridley Scott', 'duration' => '1h 57min'
        ]); 
        MovieRank::create(['movie_id' => 1, 'visitor' => '127.0.0.1', 'rank' => 10]); 

        Movie::create(['identifier' => 'matrix', 'title' => 'Matrix', 
            'year' => 1999, 'director' => 'The Wachowski Brothers', 'duration' => '2h 26min'
        ]);
        MovieRank::create(['movie_id' => 2, 'visitor' => '127.0.0.1', 'rank' => 10]);   
        
        Movie::create(['identifier' => 'the-shawshank-redemption', 'title' => 'The Shawshank Redemption', 
            'year' => 1994, 'director' => 'Frank Darabont', 'duration' => '2h 22min'
        ]);   
        MovieRank::create(['movie_id' => 3, 'visitor' => '127.0.0.1', 'rank' => 10]);  

        Movie::create(['identifier' => 'willy-wonka-and-the-chocolate-factory', 'title' => 'Willy Wonka & the Chocolate Factory', 
            'year' => 1971, 'director' => 'Mel Stuart', 'duration' => '1h 40min '
        ]);   
        MovieRank::create(['movie_id' => 4, 'visitor' => '127.0.0.1', 'rank' => 10]);  

        Movie::create(['identifier' => 'journey-to-the-center-of-the-earth', 'title' => 'Journey to the Center of the Earth', 
            'year' => 1959, 'director' => 'Henry Levin', 'duration' => '2h 2min '
        ]);   
        MovieRank::create(['movie_id' => 5, 'visitor' => '127.0.0.1', 'rank' => 10]);  

        Movie::create(['identifier' => 'outland', 'title' => 'Outland', 
            'year' => 1981, 'director' => 'Peter Hyams', 'duration' => '1h 52min '
        ]);     
        MovieRank::create(['movie_id' => 6, 'visitor' => '127.0.0.1', 'rank' => 10]);  

        Movie::create(['identifier' => 'ghost-in-the-shell', 'title' => 'Ghost in the Shell', 
            'year' => 1996, 'director' => 'Mamoru Oshii', 'duration' => '1h 23min '
        ]);       
        MovieRank::create(['movie_id' => 7, 'visitor' => '127.0.0.1', 'rank' => 10]);  

        Movie::create(['identifier' => 'spirited-away', 'title' => 'Spirited Away', 
            'year' => 2001, 'director' => 'Hayao Miyazaki', 'duration' => '2h 5min '
        ]);       
        MovieRank::create(['movie_id' => 8, 'visitor' => '127.0.0.1', 'rank' => 10]);  

        Movie::create(['identifier' => 'hakers', 'title' => 'Hackers', 
            'year' => 1995, 'director' => 'Rafael Moreu', 'duration' => '1h 47min '
        ]);       
        MovieRank::create(['movie_id' => 9, 'visitor' => '127.0.0.1', 'rank' => 11]);  

        Movie::create(['identifier' => 'dune', 'title' => 'Dune', 
            'year' => 1984, 'director' => 'David Lynch', 'duration' => '2h 17min '
        ]);     
        MovieRank::create(['movie_id' => 10, 'visitor' => '127.0.0.1', 'rank' => 10]);  

    }
}