<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function () {

//view a business
Route::get('business/{id}', 'BusinessController@show');
//create a business
Route::post('business', 'BusinessController@store');
//view businesses
Route::get('business', 'BusinessController@index');
//update business
Route::put('business/{id}', 'BusinessController@update');
//delete business
Route::delete('business/{id}', 'BusinessController@destroy');
//categories
Route::get('business/category/{category}', 'BusinessController@category');
//logout
Route::get('logout', 'AuthController@logout');
//upload images
Route::post('upload-file', ['as'=>'upload-file','uses'=>'BusinessController@uploadFile']);

});



//register
Route::post('register', 'AuthController@register');
//login
Route::post('login', 'AuthController@login');
