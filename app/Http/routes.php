<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
    Route::get('/facebook/login', 'UsersController@login');
    Route::get('/facebook/callback', 'UsersController@callback');

    Route::get('home', 'UsersController@home');
    Route::get('logout', 'UsersController@logout');

    Route::get('/auth/facebook', 'SocialController@redirectToProvider');
    Route::get('/auth/facebook/callback', 'SocialController@handleProviderCallback');
    Route::get('/auth/home', 'SocialController@home');

});


