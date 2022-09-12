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

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'nossablastwa'], function () {
    Route::post('/listDataContact', 'NossaBlastWAController@listDataContact');
    Route::get('/listDataCampaign', 'NossaBlastWAController@listDataCampaign');
    Route::get('/listDataWitel', 'NossaBlastWAController@listDataWitel');
    Route::get('/AdminContact', 'NossaBlastWAController@AdminContact')->name('AdminContact');
    Route::post('/updateCampaign', 'NossaBlastWAController@updateCampaign');
    Route::post('/addContact', 'NossaBlastWAController@addContact');
});