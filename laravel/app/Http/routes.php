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
})->name('welcome');

Route::auth();

Route::group(['middleware' => 'auth'], function() {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::resource('applications', 'ApplicationController', ['except' => ['show']]);
    Route::resource('levels', 'LevelController');
    Route::resource('units', 'UnitController');
    Route::resource('topics', 'TopicController');
    Route::resource('lessons', 'LessonController');
    Route::resource('questions', 'QuestionController');
    Route::resource('placements', 'PlacementController');
    Route::get('/create-answer/questions-bank', 'AnswerController@insertAnswerFromQuestions');
    Route::any('/questions/uploadImage', 'QuestionController@uploadImage');

    Route::get('users', 'UserController@index')->name('users.index');
    Route::get('users/create', 'UserController@create')->name('users.create');
    Route::post('users', 'UserController@store')->name('users.store');
    Route::get('users/{user}', 'UserController@show')->name('users.show');
    Route::get('users/{user}/edit', 'UserController@edit')->name('users.edit');
    Route::patch('users/{user}', 'UserController@update')->name('users.update');
    Route::delete('users/{user}', 'UserController@destroy')->name('users.destroy');

    Route::get('students', 'StudentController@index')->name('students.index');
    Route::get('students/{student}', 'StudentController@show')->name('students.show');
    Route::patch('students/super/{student}', 'StudentController@superUpdate')->name('students.super');
    Route::post('students/reset/{student}', 'StudentController@resetProgress')->name('students.reset');
    Route::post('students/delete/{student}', 'StudentController@delete')->name('students.delete');

    Route::get('settings', 'SettingController@index')->name('settings.index');
    Route::patch('settings', 'SettingController@update')->name('settings.update');

    Route::get('error_report/{type}', 'ReportErrorController@index')->name('error_report.index');
    Route::get('error_report/{type}/{id}', 'ReportErrorController@updateStatus')->name('error_report.update_status');

    Route::post('upload-icon', 'FileController@uploadTopicIcon')->name('file.upload-icon');
    Route::post('delete-icon', 'FileController@deleteTopicIcon')->name('file.delete-icon');
});
