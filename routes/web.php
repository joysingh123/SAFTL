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

Route::get('/', 'Auth\LoginController@showLoginForm');
Route::post('/login', 'Auth\LoginController@login')->middleware(['isAdmin']);

Route::get('importcompaniesdata',"ImportDataController@importComapniesWithDomainView")->middleware(['auth','isAdmin']);
Route::post('importcompaniesdata',"ImportDataController@importComapniesWithDomainData")->name('importcompaniesdata')->middleware(['auth','isAdmin']);

Route::get('importcontactdata',"ImportDataController@importContactView")->middleware(['auth','isAdmin']);
Route::post('importcontactdata',"ImportDataController@importContactData")->name('importcontactdata')->middleware(['auth','isAdmin']);
Route::post('exportjunkcontactdata',"ImportDataController@exportContactData")->name('exportjunkcontactdata');

Route::get('extractdata',"ExtractDataController@extractDataView")->name('extractdata')->middleware(['auth','isAdmin']);
Route::post('extractdata',"ExtractDataController@extractData")->name('extractdata')->middleware(['auth','isAdmin']);
Route::get('extractautocomplatedata/{data}',"ExtractDataController@extractDataForAutoComplate")->middleware(['auth','isAdmin']);

Route::get('changedomain',"ChangeDomainContoller@changeDomainView")->name('changedomain')->middleware(['auth','isAdmin']);
Route::get('editcompany/{id}',"ChangeDomainContoller@editCompanyView")->name('changedomain')->middleware(['auth','isAdmin']);
Route::post('editcompany',"ChangeDomainContoller@editCompany")->name('editcompany')->middleware(['auth','isAdmin']);
Route::post('changedomain',"ChangeDomainContoller@changeDomain")->name('changedomain')->middleware(['auth','isAdmin']);
Route::get('extractautocompletedataforchangedomain/{data}',"ChangeDomainContoller@extractDataForAutoComplete")->middleware(['auth','isAdmin']);

Route::get('domainapproval',"DomainApprovalContoller@approveDomainView")->name('domainapproval')->middleware(['auth','isAdmin']);
Route::get('approvedomainforchange/{id}',"DomainApprovalContoller@approveDomain")->name('approvedomainforchange')->middleware(['auth','isAdmin']);


Route::get('importemaildata',"ImportDataController@importEmailView")->middleware(['auth','isAdmin']);
Route::post('importemaildata',"ImportDataController@importEmailData")->name('importemaildata')->middleware(['auth','isAdmin']);

Route::get('importemaildatadump',"ImportDataController@importEmailDataImportView")->middleware(['auth','isAdmin']);
Route::post('importemaildatadump',"ImportDataController@importEmailDataDump")->name('importemaildatadump')->middleware(['auth','isAdmin']);

Route::get('importemailforvalidation',"ImportDataController@importEmailValidationImportView")->middleware(['auth','isAdmin']);
Route::post('importemailforvalidation',"ImportDataController@importEmailForValidation")->name('importemailvalidationdump')->middleware(['auth','isAdmin']);

Route::get('importbounceemaildata',"ImportDataController@importBounceEmailView")->middleware(['auth','isAdmin']);
Route::post('importbounceemaildata',"ImportDataController@importBounceEmailData")->name('importbounceemaildata')->middleware(['auth','isAdmin']);

Route::get('contactcompanymatch',"ContactCompanyMatchController@index")->middleware(['auth','isAdmin']);
Route::get('makeemailformat',"MakeEmailFormatController@index")->middleware(['auth','isAdmin']);
Route::get('reprocesssheetdata',"ReprocessSheetDataController@index")->middleware(['auth','isAdmin']);
Route::get('createemail',"CreateEmailController@index")->middleware(['auth','isAdmin']);
Route::resource('users',"UserController")->middleware(['auth','isAdmin']);
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::post('webhook', 'WebHookController@handle');

Route::get('filtercompanydata',"ChangeDomainContoller@companyFilteredData")->middleware(['auth']);

Route::get('installbase',"InstallBaseController@installBaseView")->name('installbase')->middleware(['auth','isAdmin']);
Route::post('importinstallbase',"InstallBaseController@importInstallBase")->name('importinstallbase')->middleware(['auth','isAdmin']);

Route::get('event',"InstallBaseController@eventView")->name('event')->middleware(['auth','isAdmin']);
Route::post('importevent',"InstallBaseController@importEvent")->name('importevent')->middleware(['auth','isAdmin']);

Route::get('partner',"InstallBaseController@partnerView")->name('partner')->middleware(['auth','isAdmin']);
Route::post('importpartner',"InstallBaseController@importPartner")->name('importpartner')->middleware(['auth','isAdmin']);
Route::get('companygroup',"CompanyGroupController@companyGroupView")->name('companygroup')->middleware(['auth','isAdmin']);
Route::post('importcompanygroup',"CompanyGroupController@importCompanyGroup")->name('importcompanygroup')->middleware(['auth','isAdmin']);
