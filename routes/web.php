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
});*/

//Route::middleware(['web', 'auth'])->group(function () {
Route::middleware(['web'])->group(function () {
    Route::get('/home', 'HomeController@index')->name('home');
});

/* 
    т.к. приложение- одностраничник, то при обновлении страницы надо перекидывать 
    на главную, иначе будет 404
*/
Route::any('{query}',
    function() { return redirect('/'); })
    ->where('query', '.*');
