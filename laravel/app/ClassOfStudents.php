<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClassOfStudents extends Model
{
    protected $table = 'classes';

    protected $fillable = ['teacher_id', 'key', 'name', 'class_type', 'subscription_type', 'invitations', 'is_researchable'];

    public function teacher() {
        return $this->belongsTo('App\Student', 'teacher_id');
    }

    public function teachers() {
        return $this->belongsToMany('App\Student', 'classes_teachers', 'class_id', 'student_id')
            ->withPivot(['is_researcher', 'receive_emails_from_students']);
    }

    public function teachersWithoutResearchers() {
        return $this->belongsToMany('App\Student', 'classes_teachers', 'class_id', 'student_id')
            ->withPivot(['is_researcher', 'receive_emails_from_students'])
            ->wherePivot('is_researcher', 0);
    }

    public function researchers() {
        return $this->belongsToMany('App\Student', 'classes_teachers', 'class_id', 'student_id')
            ->withPivot(['is_researcher', 'receive_emails_from_students'])
            ->wherePivot('is_researcher', 1);
    }

    public function students() {
        return $this->belongsToMany('App\Student', 'classes_students', 'class_id', 'student_id')
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

    public function applications() {
        return $this->belongsToMany('App\Application', 'classes_applications', 'class_id', 'app_id')
            ->withPivot([
                'start_date',
                'start_time',
                'due_date',
                'due_time',
                'color',
                'duration',
                'password',
                'attempts',
                'is_for_selected_students'
            ]);
    }

    public function assignments() {
        return $this->belongsToMany('App\Application', 'classes_applications', 'class_id', 'app_id')
            ->withPivot([
                'start_date',
                'start_time',
                'due_date',
                'due_time',
                'color',
                'duration',
                'password',
                'attempts',
                'is_for_selected_students'
            ])
            ->where('type', 'assignment');
    }

    public function tests() {
        return $this->belongsToMany('App\Application', 'classes_applications', 'class_id', 'app_id')
            ->withPivot([
                'start_date',
                'start_time',
                'due_date',
                'due_time',
                'color',
                'duration',
                'password',
                'attempts',
                'is_for_selected_students'
            ])
            ->where('type', 'test');
    }

    public function classApplications() {
        return $this->hasMany('App\ClassApplication', 'class_id', 'id');
    }

    public function classStudents() {
        return $this->hasMany('App\ClassStudent', 'class_id', 'id');
    }

    public function delete()
    {
        DB::table('classes_applications')->where('class_id', $this->id)->delete();
        DB::table('classes_students')->where('class_id', $this->id)->delete();
        return parent::delete();
    }

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->key)) {
                $model->key = Str::uuid()->toString(); // generate key
            }
        });
    }

    public function replicateWithRelations($new_teacher_id = false): ClassOfStudents {
        $copy = $this->replicate();
        $copy->key = null;
        if($new_teacher_id) {
            $copy->teacher_id = $new_teacher_id;
            $copy->name = 'Copy of - ' . $this->name;
            $copy->invitations = null;
        }else{
            $copy->name = $this->name . ' - Copy';
        }
        $copy->push();
        foreach (ClassApplication::where('class_id', $this->id)->get() as $row) {
            if($new_teacher_id) {
                $new_app = $row->application->replicateWithRelations($new_teacher_id);
                $app_id = $new_app->id;
            }else{
                $app_id = $row->app_id;
            }

            $relation = DB::table('classes_applications')->insert([
                'class_id' => $copy->id,
                'app_id' => $app_id,
                'start_date' => $row->start_date,
                'start_time' => $row->start_time,
                'due_date' => $row->due_date,
                'due_time' => $row->due_time,
                'duration' => $row->duration,
                'attempts' => $row->attempts,
                'color' => $row->color,
                'is_for_selected_students' => $row->is_for_selected_students
            ]);
        }
        return $copy;
    }
}
