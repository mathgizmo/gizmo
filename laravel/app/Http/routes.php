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

Route::group(['middleware' => 'auth'], function() {
    Route::get('/home', 'HomeController@index');
    Route::resource('level_views', 'LevelController');
    Route::resource('unit_views', 'UnitController');
    Route::resource('topic_views', 'TopicController');
    Route::resource('lesson_views', 'LessonController');
    Route::resource('question_views', 'QuestionController');
    Route::resource('answer_views', 'AnswerController');
    Route::get('/create-answer/questions-bank', 'AnswerController@insertAnswerFromQuestions');
    Route::any('/question_views/uploadImage', 'QuestionController@uploadImage');

    Route::get('users','UserController@index')->name('users.index');
    Route::get('users/create','UserController@create')->name('users.create');
    Route::post('users', 'UserController@store')->name('users.store');
    Route::get('users/{user}','UserController@show')->name('users.show');
    Route::get('users/{user}/edit','UserController@edit')->name('users.edit');
    Route::patch('users/{user}','UserController@update')->name('users.update');
    Route::delete('users/{user}','UserController@destroy')->name('users.destroy');

    Route::get('students','StudentController@index')->name('students.index');
    Route::get('students/{student}','StudentController@show')->name('students.show');
});
