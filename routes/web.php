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

Route::get('/', 'ApisController@index');
Route::get('/getrestaurants', 'ApisController@getRestaurants');
Route::get('/getbyradius/{lat}/{lng}/{radius}', 'ApisController@getByRadius');
Route::get('/getbytype/{lat}/{lng}/{type}', 'ApisController@getByType');
Route::get('/getbyspecific/{lat}/{lng}', 'ApisController@getBySpecific');
Route::get('/getplaces', 'ApisController@getPlaces');

// statistics - visitors
Route::get('/stat/vistors/{place_id}', 'VisitorsStatisticController@vistors');
Route::get('/stat/savevisit/{place_id}', 'VisitorsStatisticController@saveStatistics');

// statistics - foods
Route::get('/stat/foods/{place_id}', 'FoodsController@foods');
Route::get('/stat/savefood/{place_id}', 'FoodsController@saveFood');


// get review
Route::get('/review/{place_id}', 'ReviewsController@getReview');

