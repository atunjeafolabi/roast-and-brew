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

/*
  Owner Routes. Must be at least a company owner to access these routes.
*/
Route::group(['prefix' => 'v1/admin', 'middleware' => ['auth:api', 'owner']], function(){
    /*
    |-------------------------------------------------------------------------------
    | Gets All Unprocessed Actions
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/admin/actions
    | Controller:     API\Admin\ActionsController@getActions
    | Method:         GET
    | Description:    Gets all of the unprocessed actions for a user.
    */
    Route::get('/actions', 'API\Admin\ActionsController@getActions');
    /*
    |-------------------------------------------------------------------------------
    | Approves an action
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/admin/actions/{action}/approve
    | Controller:     API\Admin\ActionsController@putApproveAction
    | Method:         PUT
    | Description:    Approves an action for a user.
    | Middleware:     Only runs if the user is authorized to approve the action.
    */
    Route::put('/actions/{action}/approve', 'API\Admin\ActionsController@putApproveAction')
        ->middleware('can:approve,action');
    /*
    |-------------------------------------------------------------------------------
    | Denies an action
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/admin/actions/{action}/deny
    | Controller:     API\Admin\ActionsController@putDenyAction
    | Method:         PUT
    | Description:    Denies an action for a user.
    | Middleware:     Only runs if the user is authorized to deny the action.
    */
    Route::put('/actions/{action}/deny', 'API\Admin\ActionsController@putDenyAction')
        ->middleware('can:deny,action');
});

/*
  Admin Routes
*/
Route::group(['prefix' => 'v1/admin', 'middleware' => ['auth:api', 'admin']], function(){

});