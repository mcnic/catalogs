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


Route::get('/home', function() {
    return redirect('/');
});

$mainPath = '/'.env('MIX_AMTEL_URL');

//Route::middleware(['web', 'auth'])->group(function () {
Route::middleware(['web'])->group(function () {
    Route::get('/dfksdfjo8w38r53riefsiousd89fy8sdfjopsjfp9sdfu9zxdfz', 'HomeController@index')->name('home');

    // Необходимо прописать все возможные маршруты, чтобы при обновлении страницы не было 404
    Route::get('/'.env('MIX_AMTEL_URL'), function () {
        return view('main');
    });

    Route::get('/'.env('MIX_AMTEL_URL').'/{type}/{firm}', function () {
        return view('main');
    });
    Route::get('/'.env('MIX_AMTEL_URL').'/{type}/{firm}/{models}', function () {
        return view('main');
    });
    Route::get('/'.env('MIX_AMTEL_URL').'/{type}/{firm}/{models}/{auto}', function () {
        return view('main');
    });


});

/* 
    т.к. приложение- одностраничник, то при обновлении страницы надо перекидывать 
    на главную, иначе будет 404
*/
/*Route::any('{query}',
    function() { return redirect('/'); })
    ->where('query', '.*');*/

Route::any('{query}',
    function() { return view('not_found'); })
    ->where('query', '.*');
