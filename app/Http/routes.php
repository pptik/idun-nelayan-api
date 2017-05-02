<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'nelayan-v1', 'middleware' => 'Cors'], function()
{
	Route::post('store', 'NelayanUserController@store');
	Route::post('editprofile/{id_user}', 'NelayanUserController@editprofile');
	Route::post('editfirstname/{id_user}', 'NelayanUserController@editfirstname');
	Route::post('editlastname/{id_user}', 'NelayanUserController@editlastname');
	Route::post('edittempatlahir/{id_user}', 'NelayanUserController@edittempatlahir');
	Route::post('edittanggallahir/{id_user}', 'NelayanUserController@edittanggallahir');
	Route::post('editfoto/{id_user}', 'NelayanUserController@editfoto');
	Route::post('editnoidentitas/{id_user}', 'NelayanUserController@editnoidentitas');
	Route::post('authenticate', 'NelayanUserController@authenticate');
	Route::get('/profileshowbyid/{id_user}', 'NelayanUserController@profileshowbyid');
	Route::get('/showriwayatbyid/{id_user}', 'NelayanUserController@showriwayatbyid');
	Route::get('/jenisikan', 'NelayanUserController@getjenisikan');

});

Route::group(['prefix' => 'location-v1', 'middleware' => 'Cors'], function()
{
	
	Route::post('mapview', 'NelayanUserController@mapview');
});
Route::group(['prefix' => 'tpi-v1', 'middleware' => 'Cors'], function()
{
	Route::get('/userjointpi/{id_user}/{id_tpi}', 'NelayanUserController@userjointpi');
	Route::get('getdatatpi', 'NelayanUserController@getdatatpi');
	Route::get('getrequest', 'NelayanUserController@getrequestdatatpi');
	Route::get('getdatatpi/provinsi', 'NelayanUserController@getdatatpiprovinsi');
	Route::get('getdatatpi/kota', 'NelayanUserController@getdatatpikota');
	Route::get('getdatatpi/lokasi', 'NelayanUserController@getdatatpilokasi');
	Route::get('getdatatpi/nama', 'NelayanUserController@getdatatpinama');
	Route::post('ketersediaan', 'NelayanUserController@inputketersediaan');
	Route::post('kebutuhan', 'NelayanUserController@inputkebutuhan');
	Route::get('getkebutuhan/{id_tpi}', 'NelayanUserController@getkebutuhan');
});
Route::group(['prefix' => 'kejahatan-v1', 'middleware' => 'Cors'], function()
{
	Route::post('store', 'NelayanUserController@kejahatanstore');
});
Route::group(['prefix' => 'cuaca-v1', 'middleware' => 'Cors'], function()
{
	Route::post('store', 'NelayanUserController@cuacastore');
});
Route::group(['prefix' => 'keadaanlaut-v1', 'middleware' => 'Cors'], function()
{
	Route::post('store', 'NelayanUserController@keadaanlautstore');
});
Route::group(['prefix' => 'panicbutton-v1', 'middleware' => 'Cors'], function()
{
	Route::post('store', 'NelayanUserController@panicbuttonstore');
});
Route::group(['prefix' => 'hasiltangkapan-v1', 'middleware' => 'Cors'], function()
{
	Route::get('infotambahan', 'NelayanUserController@infotambahan');
	Route::post('store', 'NelayanUserController@hasiltangkapanstore');
	
});
