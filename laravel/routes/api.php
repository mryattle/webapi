
<?php
Route::post('login', 'Controllers\APILoginController@login');

// // REST Movies (not secure)
// Route::get('rest/movies', 'Controllers\MovieController@index');
// Route::get('rest/movies/count', 'Controllers\MovieController@count');

Route::middleware(['jwt.auth'])->group(function () {
    // REST
    Route::post('rest/movies', 'Controllers\MovieController@create');
    Route::get('rest/movies/{id}', 'Controllers\MovieController@view');
    Route::put('rest/movies/{id}', 'Controllers\MovieController@update');
    Route::delete('rest/movies/blade-runner', 'Controllers\MovieController@delete_blade_runner');
    Route::delete('rest/movies/{id}', 'Controllers\MovieController@delete');

    // RPC
    Route::post('rpc/movie.like', 'Controllers\MovieController@like');
    Route::post('rpc/movie.rank', 'Controllers\MovieController@rank');
    Route::get('rpc/movie.top10', 'Controllers\MovieController@top10');

    Route::post('refresh', 'Controllers\APILoginController@refresh');
});
        