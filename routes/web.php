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

Auth::routes();

Route::get('/', 'ThreadsController@index');
Route::get('/home', 'ThreadsController@index')->name('home');

Route::get('threads', 'ThreadsController@index');
Route::get('threads/create', 'ThreadsController@create');
Route::get('threads/{channel}', 'ThreadsController@index');
Route::post('threads', 'ThreadsController@store');
Route::get('threads/{channel}/{thread}', 'ThreadsController@show');
Route::delete('threads/{channel}/{thread}', 'ThreadsController@destroy');
Route::post('/threads/{channel}/{thread}/replies', 'RepliesController@store');

Route::get('/replies/{reply}/favorites', 'FavoritesController@loginRedirect');
Route::post('/replies/{reply}/favorites', 'FavoritesController@store');
Route::delete('/replies/{reply}', 'RepliesController@destroy');

Route::get('/profiles/{user}', 'ProfilesController@show')->name('profile');