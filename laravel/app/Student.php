<?php

namespace App;

use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;

    protected $table = 'students';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'email_new', 'password', 'country_id',
        'is_teacher', 'is_super', 'is_admin', 'is_self_study', 'is_researcher',
        'is_registered', 'email_verified_at', 'is_test_timer_displayed', 'is_test_questions_count_displayed',
        'redirect_to'
    ];

    protected $hidden = [
        'password', 'is_admin'
    ];

    public function scopeFilter($query, $filters = [])
    {
        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        if (!empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['first_name'])) {
            $query->where('first_name', 'like', '%' . $filters['first_name'] . '%');
        }

        if (!empty($filters['last_name'])) {
            $query->where('last_name', 'like', '%' . $filters['last_name'] . '%');
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        if (!empty($filters['is_super'])) {
            $query->where('is_super', $filters['is_super'] == 'yes');
        }

        if (!empty($filters['is_teacher'])) {
            $query->where('is_teacher', $filters['is_teacher'] == 'yes');
        }

        if (!empty($filters['is_researcher'])) {
            $query->where('is_researcher', $filters['is_researcher'] == 'yes');
        }

        if (!empty($filters['is_self_study'])) {
            $query->where('is_self_study', $filters['is_self_study'] == 'yes');
        }

        if (!empty($filters['is_admin'])) {
            $query->where('is_admin', $filters['is_admin'] == 'yes');
        }

        if (!empty($filters['is_registered'])) {
            $query->where('is_registered', $filters['is_registered'] == 'yes');
        }
    }

    public function students_tracking()
    {
        return $this->hasMany(StudentsTracking::class, 'student_id', 'id');
    }

    public function classes() {
        return $this->belongsToMany('App\ClassOfStudents', 'classes_students', 'student_id', 'class_id')
            ->withPivot([
                'test_duration_multiply_by',
                'is_unsubscribed',
                'is_consent_read',
                'is_element1_accepted',
                'is_element2_accepted',
                'is_element3_accepted',
                'is_element4_accepted'
            ]);
    }

    public function classTeachers() {
        return $this->hasMany('App\ClassTeacher', 'student_id', 'id');
    }

    public function classStudents() {
        return $this->hasMany('App\ClassStudent', 'student_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo('App\Country', 'country_id');
    }

    public function isTeacher()
    {
        return $this->is_teacher;
    }

    public function isResearcher()
    {
        return $this->is_researcher && $this->is_teacher;
    }

    public function isSuper()
    {
        return $this->is_super;
    }

    public function isSelfStudy()
    {
        return $this->is_self_study;
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    public function routeNotificationForMail($notification)
    {
        return $this->email_new ? $this->email_new : $this->email;
    }
}
