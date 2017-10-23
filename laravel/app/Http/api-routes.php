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
$api->any('/topic' , 'App\Http\APIControllers\TopicController@index');
$api->any('/topic/{id}' , 'App\Http\APIControllers\TopicController@get');
$api->any('/topic/{id}/lesson/{lesson_id}' , 'App\Http\APIControllers\TopicController@getLesson');

$api->group(['middleware' => 'jwt.auth'], function () use ($api) {
    $api->get('/lesson/{lesson}/start', 'App\Http\APIControllers\StudentsTrackingController@start');
    $api->get('/lesson/{lesson}/done', 'App\Http\APIControllers\StudentsTrackingController@done');
});

