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
    $user = $request->user();
    return response()->json($user);
});
Route::middleware('auth:api')->get('/emailvalidationapikey', 'ApiController@getEmailValidationApiKey');
Route::middleware('auth:api')->post('/getemailformatbydomain', 'ApiController@getEmailFormatByDomain');
Route::middleware('auth:api')->post('/getemailinfo', 'ApiController@getEmailInfo');
Route::middleware('auth:api')->post('/verifyemail', 'ApiController@verifyEmail');