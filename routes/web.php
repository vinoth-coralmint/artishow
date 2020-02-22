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


Route::get('ical', 'IcalController@index')->name('ical');
Route::get('ical/sync', 'IcalController@autoSync')->name('autoSync');

// Artishow woo-commerce (Rename the route and controller as per your wish)
Route::get('woo-commerce', 'WooCommerceController@index');
Route::get('woo-commerce/sync', 'WooCommerceController@autoSync');