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

/*
  Public API Routes
*/
Route::group(['prefix' => 'v1'], function(){

    Route::get('/user', function( Request $request ){
        return $request->user('api');
    });

    Route::get('/cafes', 'API\CafesController@getCafes');
    Route::get('/cafes/{id}', 'API\CafesController@getCafe');
    Route::get('/brew-methods', 'API\BrewMethodsController@getBrewMethods');
    Route::get('/tags', 'API\TagsController@getTags');

});

/*
 * Authenticated API routes
 */
Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function(){

    Route::post('/cafes', 'API\CafesController@postNewCafe');
    Route::put('/cafes/{cafeID}', 'API\CafesController@putEditCafe');
    Route::get('/cafes/{id}/edit', 'API\CafesController@getCafeEditData');
    Route::post('/cafes/{id}/like', 'API\CafesController@postLikeCafe');
    Route::delete('/cafes/{id}/like', 'API\CafesController@deleteLikeCafe');
    Route::post('/cafes/{id}/tags', 'API\CafesController@postAddTags');
    Route::delete('/cafes/{id}/tags/{tagID}', 'API\CafesController@deleteCafeTag');
    Route::put('/user', 'API\UsersController@putUpdateUser');
});