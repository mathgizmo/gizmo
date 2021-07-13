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
    $api->get('/welcome' , 'App\Http\APIControllers\HomeController@getWelcomeTexts');
    $api->get('/countries' , 'App\Http\APIControllers\HomeController@getCountries');
    $api->post('/login/by-token' , 'App\Http\APIControllers\AuthController@loginByToken');
    $api->post('/login' , 'App\Http\APIControllers\AuthController@login');
    $api->post('/register' , 'App\Http\APIControllers\AuthController@register');
    $api->get('/logout' , 'App\Http\APIControllers\AuthController@logout');
    $api->post('/password-reset-email' , 'App\Http\APIControllers\AuthController@passwordResetEmail');
    $api->post('/reset-password' , 'App\Http\APIControllers\AuthController@resetPassword');
    $api->get('email-verify/{id}', 'App\Http\APIControllers\AuthController@verifyEmail')->name('verification.verify');
    $api->post('email-verify', 'App\Http\APIControllers\AuthController@resendVerificationEmail')->name('verification.resend');
    $api->post('email/check', 'App\Http\APIControllers\AuthController@checkEmail');

    $api->group(['middleware' => ['api.auth', 'verified']], function () use ($api) {
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
        $api->any('/topic/{id}/testout/done' , 'App\Http\APIControllers\TopicController@testoutDone');
        $api->post('/topic/{id}/testout/done-lessons' , 'App\Http\APIControllers\StudentsTrackingController@doneTestoutLessons');

        $api->post('/lesson/{lesson}/start', 'App\Http\APIControllers\StudentsTrackingController@start');
        $api->post('/lesson/{lesson}/done', 'App\Http\APIControllers\StudentsTrackingController@done');
        $api->post('/question/{question_id}/tracking', 'App\Http\APIControllers\StudentsTrackingController@trackQuestionAnswer');

        $api->post('/report_error/{question}', 'App\Http\APIControllers\ReportErrorController@report');

        $api->get('/profile', 'App\Http\APIControllers\ProfileController@get');
        $api->post('/profile', 'App\Http\APIControllers\ProfileController@update');
        $api->post('/profile/app', 'App\Http\APIControllers\ProfileController@changeApplication');
        $api->get('/profile/todo', 'App\Http\APIControllers\ProfileController@getToDos');
        $api->get('/profile/tests', 'App\Http\APIControllers\ProfileController@getTests');
        $api->post('/profile/tests/{test_id}/reveal', 'App\Http\APIControllers\ProfileController@revealTest');
        $api->post('/profile/options', 'App\Http\APIControllers\ProfileController@changeOptions');
        $api->get('/profile/classes', 'App\Http\APIControllers\ProfileController@getClasses');
        $api->get('/profile/classes/invitations', 'App\Http\APIControllers\ProfileController@getClassInvitations');
        $api->post('/profile/classes/{class_id}/subscribe', 'App\Http\APIControllers\ProfileController@subscribeClass');
        $api->post('/profile/classes/{class_id}/unsubscribe', 'App\Http\APIControllers\ProfileController@unsubscribeClass');
        $api->get('/profile/classes/{class_id}/assignments-report.{format}', 'App\Http\APIControllers\ProfileController@downloadAssignmentsReport');
        $api->get('/profile/classes/{class_id}/tests-report.{format}', 'App\Http\APIControllers\ProfileController@downloadTestsReport');

        $api->get('/classes', 'App\Http\APIControllers\ClassController@all');
        $api->post('/classes', 'App\Http\APIControllers\ClassController@store');
        $api->put('/classes/{class_id}', 'App\Http\APIControllers\ClassController@update');
        $api->delete('/classes/{class_id}', 'App\Http\APIControllers\ClassController@delete');

        $api->post('/classes/{class_id}/email', 'App\Http\APIControllers\ClassController@emailClass');

        $api->get('/classes/{class_id}/threads', 'App\Http\APIControllers\ClassThreadController@index');
        $api->post('/classes/{class_id}/threads', 'App\Http\APIControllers\ClassThreadController@store');
        $api->put('/classes/{class_id}/threads/{thread_id}', 'App\Http\APIControllers\ClassThreadController@update');
        $api->delete('/classes/{class_id}/threads/{thread_id}', 'App\Http\APIControllers\ClassThreadController@destroy');
        $api->get('/classes/{class_id}/threads/{thread_id}', 'App\Http\APIControllers\ClassThreadController@getReplies');
        $api->post('/classes/{class_id}/threads/{thread_id}/reply', 'App\Http\APIControllers\ClassThreadController@storeReply');
        $api->put('/classes/{class_id}/threads/{thread_id}/reply/{reply_id}', 'App\Http\APIControllers\ClassThreadController@updateReply');
        $api->delete('/classes/{class_id}/threads/{thread_id}/reply/{reply_id}', 'App\Http\APIControllers\ClassThreadController@destroyReply');

        $api->get('/classes/{class_id}/students', 'App\Http\APIControllers\ClassController@getStudents');
        $api->post('/classes/{class_id}/students', 'App\Http\APIControllers\ClassController@addStudents');
        $api->put('/classes/{class_id}/students/{student_id}', 'App\Http\APIControllers\ClassController@updateStudent');
        $api->delete('/classes/{class_id}/students/{student_id}', 'App\Http\APIControllers\ClassController@deleteStudent');
        $api->get('/classes/{class_id}/students/{student_id}/report/assignments', 'App\Http\APIControllers\ClassController@getStudentAssignmentsReport');
        $api->get('/classes/{class_id}/students/{student_id}/report/tests', 'App\Http\APIControllers\ClassController@getStudentTestsReport');

        $api->get('/classes/{class_id}/teachers', 'App\Http\APIControllers\ClassController@getTeachers');
        $api->post('/classes/{class_id}/teachers/{teacher_id}', 'App\Http\APIControllers\ClassController@addTeacherToClass');
        $api->put('/classes/{class_id}/teachers/{teacher_id}', 'App\Http\APIControllers\ClassController@updateTeacher');
        $api->delete('/classes/{class_id}/teachers/{teacher_id}', 'App\Http\APIControllers\ClassController@deleteTeacherFromClass');

        $api->get('/classes/{class_id}/assignments', 'App\Http\APIControllers\ClassController@getAssignments');
        $api->post('/classes/{class_id}/assignments/{app_id}', 'App\Http\APIControllers\ClassController@addApplicationToClass');
        $api->put('/classes/{class_id}/assignments/{app_id}', 'App\Http\APIControllers\ClassController@changeAssignment');
        $api->put('/classes/{class_id}/assignments/{app_id}/students', 'App\Http\APIControllers\ClassController@changeApplicationStudents');
        $api->delete('/classes/{class_id}/assignments/{app_id}', 'App\Http\APIControllers\ClassController@deleteApplicationFromClass');
        $api->get('/classes/{class_id}/assignments-report.{format}', 'App\Http\APIControllers\ClassController@downloadAssignmentsReport');
        $api->get('/classes/{class_id}/tests-report.{format}', 'App\Http\APIControllers\ClassController@downloadTestsReport');
        $api->get('/classes/{class_id}/tests', 'App\Http\APIControllers\ClassController@getTests');
        $api->post('/classes/{class_id}/tests/{app_id}', 'App\Http\APIControllers\ClassController@addApplicationToClass');
        $api->put('/classes/{class_id}/tests/{app_id}', 'App\Http\APIControllers\ClassController@changeTest');
        $api->put('/classes/{class_id}/tests/{app_id}/students', 'App\Http\APIControllers\ClassController@changeApplicationStudents');
        $api->delete('/classes/{class_id}/tests/{app_id}', 'App\Http\APIControllers\ClassController@deleteApplicationFromClass');
        $api->get('/classes/{class_id}/tests/{app_id}/report', 'App\Http\APIControllers\ClassController@getTestReport');
        $api->post('classes/{class_id}/tests/{app_id}/student/{student_id}/reset', 'App\Http\APIControllers\ClassController@resetTestProgress');
        $api->get('/classes/{class_id}/tests/{app_id}/student/{student_id}/details', 'App\Http\APIControllers\ClassController@getTestDetails');
        $api->get('/classes/{class_id}/tests/{app_id}/student/{student_id}/report.pdf', 'App\Http\APIControllers\ClassController@downloadTestReportPDF');
        // $api->get('/classes/{class_id}/tests/{app_id}/poor-questions-report.pdf', 'App\Http\APIControllers\ClassController@downloadTestPoorQuestionsReportPDF');
        $api->get('/classes/{class_id}/report', 'App\Http\APIControllers\ClassController@getReport');
        $api->get('/classes/{class_id}/answers-statistics', 'App\Http\APIControllers\ClassController@getAnswersStatistics');

        $api->get('/assignments', 'App\Http\APIControllers\ApplicationController@getAssignments');
        $api->post('/assignments', 'App\Http\APIControllers\ApplicationController@storeAssignment');
        $api->post('/assignments/{app_id}/copy', 'App\Http\APIControllers\ApplicationController@copy');
        $api->put('/assignments/{app_id}', 'App\Http\APIControllers\ApplicationController@update');
        $api->delete('/assignments/{app_id}', 'App\Http\APIControllers\ApplicationController@delete');
        $api->get('/assignments/{app_id}/tree', 'App\Http\APIControllers\ApplicationController@getAppTree');

        $api->post('/tests/get-questions-count', 'App\Http\APIControllers\ApplicationController@getQuestionsCount');
        $api->post('/tests/{test_id}/start', 'App\Http\APIControllers\ApplicationController@startTest');
        $api->post('/tests/{test_id}/track', 'App\Http\APIControllers\ApplicationController@trackTest');
        $api->post('/tests/{test_id}/finish', 'App\Http\APIControllers\ApplicationController@finishTest');
        $api->get('/tests', 'App\Http\APIControllers\ApplicationController@getTests');
        $api->post('/tests', 'App\Http\APIControllers\ApplicationController@storeTest');
        $api->post('/tests/{app_id}/copy', 'App\Http\APIControllers\ApplicationController@copy');
        $api->put('/tests/{app_id}', 'App\Http\APIControllers\ApplicationController@update');
        $api->delete('/tests/{app_id}', 'App\Http\APIControllers\ApplicationController@delete');
        $api->get('/tests/{app_id}/tree', 'App\Http\APIControllers\ApplicationController@getAppTree');

        $api->get('/content' , 'App\Http\APIControllers\ContentController@index');
        $api->get('/dashboard' , 'App\Http\APIControllers\DashboardController@index');
        $api->get('/tutorial' , 'App\Http\APIControllers\TutorialController@index');
        $api->get('/faq' , 'App\Http\APIControllers\FaqController@index');

        $api->get('/available-icons', 'App\Http\APIControllers\ApplicationController@getAvailableIcons');
    });
});
