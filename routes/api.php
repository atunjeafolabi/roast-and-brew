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

    Route::get('/users', 'API\UsersController@getUsers');

    Route::get('/cafes', 'API\CafesController@getCafes');
    Route::get('/cafes/{slug}', 'API\CafesController@getCafe');
    Route::get('/brew-methods', 'API\BrewMethodsController@getBrewMethods');
    Route::get('/tags', 'API\TagsController@getTags');

});

/*
 * Authenticated API routes
 */
Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function(){

    Route::post('/cafes', 'API\CafesController@postNewCafe');
    Route::put('/cafes/{slug}', 'API\CafesController@putEditCafe');
    Route::get('/cafes/{slug}/edit', 'API\CafesController@getCafeEditData');
    Route::post('/cafes/{slug}/like', 'API\CafesController@postLikeCafe');
    Route::delete('/cafes/{slug}/like', 'API\CafesController@deleteLikeCafe');
    Route::post('/cafes/{slug}/tags', 'API\CafesController@postAddTags');
    Route::delete('/cafes/{slug}/tags/{tagID}', 'API\CafesController@deleteCafeTag');
    Route::delete('/cafes/{slug}', 'API\CafesController@deleteCafe');
    Route::put('/user', 'API\UsersController@putUpdateUser');
    Route::get('/companies/search', 'API\CompaniesController@getCompanySearch');
});