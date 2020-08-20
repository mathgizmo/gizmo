<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable implements JWTSubject
{
    protected $table = 'students';

    protected $fillable = [
        'first_name', 'last_name', 'name', 'email', 'password', 'question_num', 'country_id', 'is_teacher', 'is_super', 'is_admin'
    ];

    protected $hidden = [
        'password', 'is_admin'
    ];

    public function scopeFilter($query, $filters = [])
    {
        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
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
    }

    public function students_tracking()
    {
        return $this->hasMany(StudentsTracking::class, 'student_id', 'id');
    }

    public function classes() {
        return $this->belongsToMany('App\ClassOfStudents', 'classes_students', 'student_id', 'class_id');
    }

    public function country()
    {
        return $this->belongsTo('App\Country', 'country_id');
    }

    public function isTeacher()
    {
        return $this->is_teacher;
    }

    public function isSuper()
    {
        return $this->is_super;
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
}
