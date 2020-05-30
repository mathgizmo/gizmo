<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    protected $table = 'students';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'name', 'email', 'password', 'question_num', 'is_teacher', 'is_super', 'is_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
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
}
