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

$api->get('/' , 'App\Http\APIControllers\HomeController@index');
$api->any('/authenticate' , 'App\Http\APIControllers\AuthController@authenticate');
$api->any('/register' , 'App\Http\APIControllers\AuthController@register');
$api->post('/password-reset-email' , 'App\Http\APIControllers\AuthController@passwordResetEmail');
$api->post('/reset-password' , 'App\Http\APIControllers\AuthController@resetPassword');

$api->group(['middleware' => 'api.auth'], function () use ($api) {
    $api->any('/topic' , 'App\Http\APIControllers\TopicController@index');
    $api->any('/topic/{id}' , 'App\Http\APIControllers\TopicController@get');
    $api->any('/topic/{id}/lesson/{lesson_id}' , 'App\Http\APIControllers\TopicController@getLesson');
    $api->any('/topic/{id}/testout' , 'App\Http\APIControllers\TopicController@testout');
    $api->any('/topic/{id}/testoutdone' , 'App\Http\APIControllers\TopicController@testoutdone');

    $api->post('/lesson/{lesson}/start', 'App\Http\APIControllers\StudentsTrackingController@start');
    $api->post('/lesson/{lesson}/done', 'App\Http\APIControllers\StudentsTrackingController@done');

    $api->post('/report_error/{question}', 'App\Http\APIControllers\ReportErrorController@report');

    $api->get('/profile', 'App\Http\APIControllers\ProfileController@get');
    $api->post('/profile', 'App\Http\APIControllers\ProfileController@update');
});

