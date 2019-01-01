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

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

Route::get('/login','UserController@showlogin');

// route to show the login form
Route::post('login', 'UserController@checkuser');

Route::post('register','UserController@register');

Route::post('question','UserController@addquestion');

Route::get('display','UserController@dispque');

Route::get('displayall','UserController@dispallque');

Route::get('saveans','UserController@saveans');

Route::post('saveans','UserController@saveansdata');

Route::post('savevote','UserController@savevote');

Route::get('search','UserController@searchbyquestiontext');

Route::post('comment','UserController@savecomment');

Route::get('logout','UserController@logout');

//Route::get('login', 'HomeController@showLogin');

// route to process the form
//Route::post('login', array('uses' => 'HomeController@doLogin'));
