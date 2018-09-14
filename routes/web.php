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

Route::get('importcompaniesdata',"ImportDataController@importComapniesWithDomainView")->middleware(['auth','isAdmin']);
Route::post('importcompaniesdata',"ImportDataController@importComapniesWithDomainData")->name('importcompaniesdata')->middleware(['auth','isAdmin']);

Route::get('importcontactdata',"ImportDataController@importContactView")->middleware('auth');
Route::post('importcontactdata',"ImportDataController@importContactData")->name('importcontactdata')->middleware('auth');
Route::post('exportjunkcontactdata',"ImportDataController@exportContactData")->name('exportjunkcontactdata');

Route::get('extractdata',"ExtractDataController@extractDataView")->name('extractdata')->middleware(['auth','isAdmin']);
Route::post('extractdata',"ExtractDataController@extractData")->name('extractdata')->middleware(['auth','isAdmin']);
Route::get('extractautocomplatedata/{data}',"ExtractDataController@extractDataForAutoComplate")->middleware(['auth','isAdmin']);


Route::get('importemaildata',"ImportDataController@importEmailView")->middleware(['auth','isAdmin']);
Route::post('importemaildata',"ImportDataController@importEmailData")->name('importemaildata')->middleware(['auth','isAdmin']);

Route::get('contactcompanymatch',"ContactCompanyMatchController@index")->middleware(['auth','isAdmin']);
Route::get('makeemailformat',"MakeEmailFormatController@index")->middleware(['auth','isAdmin']);
Route::get('createemail',"CreateEmailController@index")->middleware(['auth','isAdmin']);
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
