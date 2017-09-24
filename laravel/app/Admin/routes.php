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

Route::auth();

Route::get('/home', 'HomeController@index');
Route::resource('level_views', 'LevelController');
Route::resource('unit_views', 'UnitController');
Route::resource('topic_views', 'TopicController');
Route::resource('lesson_views', 'LessonController');
Route::resource('question_views', 'QuestionController');

