<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes(['register' => false]);
Route::get('/logout', 'Auth\LoginController@logout');

Route::group(['middleware' => 'auth'], function() {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::resource('applications', 'ApplicationController', ['except' => ['show']]);
    Route::post('applications/{application}/copy', 'ApplicationController@copy')->name('applications.copy');
    Route::get('applications/search', 'ApplicationController@find')->name('applications.search');
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
    Route::get('students/search', 'StudentController@find')->name('students.search');
    Route::get('students/{student}/edit', 'StudentController@edit')->name('students.edit');
    Route::get('students/{student}/login', 'StudentController@loginAsStudent')->name('students.login');
    Route::patch('students/super/{student}', 'StudentController@superUpdate')->name('students.super');
    Route::patch('students/self-study/{student}', 'StudentController@selfStudyUpdate')->name('students.self-study');
    Route::patch('students/teacher/{student}', 'StudentController@teacherUpdate')->name('students.teacher');
    Route::patch('students/researcher/{student}', 'StudentController@researcherUpdate')->name('students.researcher');
    Route::post('students/reset/{student}', 'StudentController@resetProgress')->name('students.reset');
    Route::post('students/delete/{student}', 'StudentController@delete')->name('students.delete');

    Route::resource('classes', 'ClassController', ['except' => ['show']]);
    Route::get('classes/search', 'ClassController@find')->name('classes.search');
    Route::get('classes/{class_id}/students', 'ClassController@getStudents')->name('classes.students.index');

    Route::get('settings', 'SettingController@index')->name('settings.index');
    Route::patch('settings', 'SettingController@update')->name('settings.update');

    Route::resource('dashboards', 'DashboardController');
    Route::resource('faqs', 'FaqController');
    Route::resource('tutorials', 'TutorialController');

    Route::get('error_report/{type}', 'ReportErrorController@index')->name('error_report.index');
    Route::get('error_report/{type}/{id}', 'ReportErrorController@updateStatus')->name('error_report.update_status');

    Route::post('upload-icon', 'FileController@uploadTopicIcon')->name('file.upload-icon');
    Route::post('delete-icon', 'FileController@deleteTopicIcon')->name('file.delete-icon');

    Route::resource('mails', 'MailController', ['except' => ['show', 'create', 'store', 'destroy']]);
    Route::get('mails/new', 'MailController@newMail')->name('mails.new');
    Route::post('mails/send', 'MailController@sendMail')->name('mails.send');
});

Route::group(['prefix'=>'job','as'=>'job.'], function() {
    Route::delete('statistics/answers', 'JobController@deleteOldAnswersStatistics')
        ->name('statistics.answers.delete')->middleware('auth');
    Route::get('reports/class-detailed/generate', 'JobController@generateClassDetailedReports')
        ->name('reports.class-detailed.generate');
    Route::get('tests/check-timeout', 'JobController@checkTestsTimeout')
        ->name('tests.check-timeout');
});
