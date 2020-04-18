<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutoimportController;
use App\Http\Controllers\AtmtelController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// for 1c:
Route::group(['middleware' => 'checkIp:api'], function () {
    Route::get('/basket', 'AtmtelController@getBasket');
    Route::post('/basketAdd/{goods_id}/{goods_supplier_sh_id}/{supplier_point_id}/{count}', 'AtmtelController@add2Basket');
    // goods_list = goods_id's array, etc [46889,46915,46892,46936]
    Route::delete('/basketDel/{goods_list}', 'AtmtelController@delFromBasket');

    Route::get('/order', 'AtmtelController@getOrder');
    Route::post('/orderAdd/{goods_list}', 'AtmtelController@add2Order');
    Route::delete('/orderDel/{goods_id}', 'AtmtelController@delFromOrder');
    Route::post('/orderConfirm/{goods_id}', 'AtmtelController@confirmOrder');
});

//Route::middleware(['auth:api'])->group(function () {
//Route::group(['middleware' => 'myapi'], function () {
Route::group(['middleware' => getenv('AMTEL_API_MIDDLEWARE', 'checkIp:myapi')], function () {
    /*Route::get('/user', function (Request $request) {
        return $request->user();
    });*/

    //Route::get('/huIsUser', 'AutoimportController@getUser');

    Route::get('/firm', 'AtmtelController@getFirm');
    Route::get('/modelGroups/{firm}', 'AtmtelController@getModelGroups');
    Route::get('/models/{typeAutos}/{firm}/{modelGroup}', 'AtmtelController@getModels');
    Route::get('/model/{modelUrl}', 'AtmtelController@getModel');
    Route::get('/goodsList/{modelId}', 'AtmtelController@getGoodsList');
    //выдача полной инфы от поставщика, как есть. потом закрыть метод
    //Route::get('/goodsAll/{modelId}/{goodId}', 'AtmtelController@getGoodsAll'); 
    Route::get('/goods/{modelId}/{goodId}', 'AtmtelController@getGoods');
    Route::get('/goodsByNum/{num}', 'AtmtelController@getGoodsByNum');
});
