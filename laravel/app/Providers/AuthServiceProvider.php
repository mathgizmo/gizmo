<?php

namespace App\Providers;

use App\Application;
use App\Lesson;
use App\Level;
use App\PlacementQuestion;
use App\Policies\ApplicationPolicy;
use App\Policies\LessonPolicy;
use App\Policies\PlacementPolicy;
use App\Policies\QuestionPolicy;
use App\Policies\ReportErrorPolicy;
use App\Policies\SettingPolicy;
use App\Policies\StudentPolicy;
use App\Policies\TopicPolicy;
use App\Policies\UnitPolicy;
use App\Policies\UserPolicy;
use App\Question;
use App\ReportError;
use App\Setting;
use App\Student;
use App\Topic;
use App\Unit;
use App\User;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        /* User::class => UserPolicy::class,
        Question::class => QuestionPolicy::class,
        Lesson::class => LessonPolicy::class,
        Topic::class => TopicPolicy::class,
        Unit::class => UnitPolicy::class,
        Level::class => LessonPolicy::class,
        Application::class => ApplicationPolicy::class,
        PlacementQuestion::class => PlacementPolicy::class,
        Student::class => StudentPolicy::class,
        Setting::class => SettingPolicy::class,
        ReportError::class => ReportErrorPolicy::class, */
    ];

    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);
        Gate::before(function ($user, $ability) {
            return $user->isSuperAdmin() ? true : null; // super admin permission
        });
    }
}
