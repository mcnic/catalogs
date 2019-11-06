<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/', function () {
    return view('main');
});

/*Route::get('/home', function() {
    return redirect('/');
})->name('home');*/

//Route::middleware(['web', 'auth'])->group(function () {
Route::middleware(['web'])->group(function () {

    Route::prefix('/'.env('MIX_AMTEL_PREFIX'))->group(function () {
        // Необходимо прописать все возможные маршруты, чтобы при обновлении страницы не было 404
        $amtelPaths = ['/{type}/{firm}', '/{type}/{firm}/{models}', '/{type}/{firm}/{models}/{auto}'];
        
        foreach ($amtelPaths as $path) {
            Route::get($path, function () {
                return view('main');
            });
        }
    });

});

// 404
Route::any('{query}',
    function() { return view('not_found'); })
    ->where('query', '.*');
