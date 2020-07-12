<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->get('/' , 'App\Http\APIControllers\HomeController@index');
    $api->get('/welcome' , 'App\Http\APIControllers\HomeController@getWelcomeTexts');
    $api->any('/authenticate' , 'App\Http\APIControllers\AuthController@authenticate');
    $api->any('/register' , 'App\Http\APIControllers\AuthController@register');
    $api->post('/password-reset-email' , 'App\Http\APIControllers\AuthController@passwordResetEmail');
    $api->post('/reset-password' , 'App\Http\APIControllers\AuthController@resetPassword');

    $api->group(['middleware' => 'api.auth'], function () use ($api) {
        $api->get('/lesson/last-visited/{student_id}' , 'App\Http\APIControllers\TopicController@getLastVisitedLesson');
        $api->get('/topic/last-visited/{student_id}' , 'App\Http\APIControllers\TopicController@getLastVisitedTopic');
        $api->get('/unit/last-visited/{student_id}' , 'App\Http\APIControllers\TopicController@getLastVisitedUnit');

        $api->get('/placement' , 'App\Http\APIControllers\PlacementController@get');
        $api->get('/placement/getTopicId/{unit_id}' , 'App\Http\APIControllers\PlacementController@getTopicId');
        $api->post('/placement/done-unit' , 'App\Http\APIControllers\PlacementController@doneUnit');
        $api->post('/placement/done-half-unit' , 'App\Http\APIControllers\PlacementController@doneHalfUnit');
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
        $api->post('/profile/app', 'App\Http\APIControllers\ProfileController@changeApplication');
        $api->get('/profile/todo', 'App\Http\APIControllers\ProfileController@getToDos');
        $api->get('/profile/classes', 'App\Http\APIControllers\ProfileController@getClasses');
        $api->get('/profile/classes/invitations', 'App\Http\APIControllers\ProfileController@getClassInvitations');
        $api->post('/profile/classes/{class_id}/subscribe', 'App\Http\APIControllers\ProfileController@subscribeClass');
        $api->post('/profile/classes/{class_id}/unsubscribe', 'App\Http\APIControllers\ProfileController@unsubscribeClass');

        $api->get('/classes', 'App\Http\APIControllers\ClassController@all');
        $api->post('/classes', 'App\Http\APIControllers\ClassController@store');
        $api->put('/classes/{class_id}', 'App\Http\APIControllers\ClassController@update');
        $api->delete('/classes/{class_id}', 'App\Http\APIControllers\ClassController@delete');
        $api->get('/classes/{class_id}/students', 'App\Http\APIControllers\ClassController@getStudents');
        $api->get('/classes/{class_id}/assignments', 'App\Http\APIControllers\ClassController@getAssignments');
        $api->post('/classes/{class_id}/assignments/{app_id}', 'App\Http\APIControllers\ClassController@addAssignmentToClass');
        $api->put('/classes/{class_id}/assignments/{app_id}', 'App\Http\APIControllers\ClassController@changeAssignment');
        $api->delete('/classes/{class_id}/assignments/{app_id}', 'App\Http\APIControllers\ClassController@deleteAssignmentFromClass');

        $api->get('/assignments', 'App\Http\APIControllers\ApplicationController@all');
        $api->post('/assignments', 'App\Http\APIControllers\ApplicationController@store');
        $api->put('/assignments/{app_id}', 'App\Http\APIControllers\ApplicationController@update');
        $api->delete('/assignments/{app_id}', 'App\Http\APIControllers\ApplicationController@delete');
        $api->get('/assignments/{app_id}/tree', 'App\Http\APIControllers\ApplicationController@getAppTree');

        $api->get('/available-icons', 'App\Http\APIControllers\ApplicationController@getAvailableIcons');
    });
});
