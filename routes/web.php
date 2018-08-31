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

Route::get('/', function () {
    return view('welcome');
});

Route::get('importcompaniesdata',"ImportDataController@importComapniesWithDomainView");
Route::post('importcompaniesdata',"ImportDataController@importComapniesWithDomainData")->name('importcompaniesdata');

Route::get('importcontactdata',"ImportDataController@importContactView");
Route::post('importcontactdata',"ImportDataController@importContactData")->name('importcontactdata');

Route::get('contactcompanymatch',"ContactCompanyMatchController@index");