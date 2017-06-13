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
	Route::post('store', 'NelayanUserV2Controller@store');
	Route::post('editprofile/{id_user}', 'NelayanUserV2Controller@editprofile');
	Route::post('editfirstname/{id_user}', 'NelayanUserV2Controller@editfirstname');
	Route::post('editlastname/{id_user}', 'NelayanUserV2Controller@editlastname');
	Route::post('editaddress/{id_user}', 'NelayanUserV2Controller@editaddress');
	Route::post('edittempatlahir/{id_user}', 'NelayanUserV2Controller@edittempatlahir');
	Route::post('edittanggallahir/{id_user}', 'NelayanUserV2Controller@edittanggallahir');
	Route::post('editfoto/{id_user}', 'NelayanUserV2Controller@editfoto');
	Route::post('editnoidentitas/{id_user}', 'NelayanUserV2Controller@editnoidentitas');
	Route::post('authenticate', 'NelayanUserV2Controller@authenticate');
	Route::get('/profileshowbyid/{id_user}', 'NelayanUserV2Controller@profileshowbyid');
	Route::get('/showriwayatbyid/{id_user}', 'NelayanUserV2Controller@showriwayatbyid');
	Route::get('/jenisikan', 'NelayanUserV2Controller@getjenisikan');
	Route::get('/jenislog', 'NelayanUserV2Controller@getjenislog');
	Route::get('/wilayah', 'NelayanUserV2Controller@getwilayah');
	Route::get('/gerombolanikan', 'NelayanUserV2Controller@getgerombolanikan');
	Route::get('/aktivitas', 'NelayanUserV2Controller@getaktivitas');
	Route::get('/useraktivitas/{id_user}', 'NelayanUserV2Controller@getUserAktivitas');
});

Route::group(['prefix' => 'location-v1', 'middleware' => 'Cors'], function()
{
	Route::post('mapview', 'NelayanUserV2Controller@mapview');
});
Route::group(['prefix' => 'tpi-v1', 'middleware' => 'Cors'], function()
{
	Route::get('/userjointpi/{id_user}/{id_tpi}', 'NelayanUserV2Controller@userjointpi');
	Route::get('getdatatpi', 'NelayanUserV2Controller@getdatatpi');
	Route::get('getrequest', 'NelayanUserV2Controller@getrequestdatatpi');
	Route::get('getdatatpi/provinsi', 'NelayanUserV2Controller@getdatatpiprovinsi');
	Route::get('getdatatpi/kota', 'NelayanUserV2Controller@getdatatpikota');
	Route::get('getdatatpi/lokasi', 'NelayanUserV2Controller@getdatatpilokasi');
	Route::get('getdatatpi/nama', 'NelayanUserV2Controller@getdatatpinama');
	Route::post('ketersediaan', 'NelayanUserV2Controller@inputketersediaan');
	Route::post('kebutuhan', 'NelayanUserV2Controller@inputkebutuhan');
	Route::get('getkebutuhan/{id_tpi}', 'NelayanUserV2Controller@getkebutuhan');
	Route::get('getketersediaan/{id_tpi}', 'NelayanUserV2Controller@getketersediaan');
});
Route::group(['prefix' => 'kejahatan-v1', 'middleware' => 'Cors'], function()
{
	Route::post('store', 'NelayanUserV2Controller@kejahatanstore');
});
Route::group(['prefix' => 'cuaca-v1', 'middleware' => 'Cors'], function()
{
	Route::post('store', 'NelayanUserV2Controller@cuacastore');
});
Route::group(['prefix' => 'keadaanlaut-v1', 'middleware' => 'Cors'], function()
{
	Route::post('store', 'NelayanUserV2Controller@keadaanlautstore');
});
Route::group(['prefix' => 'panicbutton-v1', 'middleware' => 'Cors'], function()
{
	Route::post('store', 'NelayanUserV2Controller@panicbuttonstore');
});
Route::group(['prefix' => 'hasiltangkapan-v1', 'middleware' => 'Cors'], function()
{
	Route::get('infotambahan', 'NelayanUserV2Controller@infotambahan');
	Route::post('store', 'NelayanUserV2Controller@hasiltangkapanstore');
	Route::post('penangkapan', 'NelayanUserV2Controller@storePenangkapan');
	Route::post('aktivitas', 'NelayanUserV2Controller@storeAktivitasPenangkapan');
	Route::post('komposisihasil', 'NelayanUserV2Controller@storeKomposisiPenangkapan');
});
Route::group(['prefix' => 'permintaanikan-v1', 'middleware' => 'Cors'], function()
{
	Route::post('store', 'NelayanUserV2Controller@inputkebutuhan');	
});
Route::group(['prefix' => 'datakapal-v1', 'middleware' => 'Cors'], function()
{
	Route::get('search/{value}', 'NelayanUserV2Controller@searchkapal');
});