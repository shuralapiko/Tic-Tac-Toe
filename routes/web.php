<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
| app\Providers\RouteServiceProvider.php
*/

Route::get('/', function () {
    return view('index');
});
Route::prefix('api')->name('api.')->group(function() {
    Route::get('/', 'GameController@latestOrCreate');
    Route::post('restart', 'GameController@restart');
    Route::post('{piece}', 'GameController@placePiece');
    Route::delete('/', 'GameController@destroy');
});
