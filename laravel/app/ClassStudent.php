<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClassStudent extends Model
{
    protected $table = 'classes_students';

    protected $fillable = ['class_id', 'student_id', 'test_duration_multiply_by', 'is_unsubscribed',
        'is_consent_read', 'is_element1_accepted', 'is_element2_accepted', 'is_element3_accepted', 'is_element4_accepted'];

    public function student() {
        return $this->belongsTo('App\Student', 'student_id', 'id');
    }

    public function classOfStudents() {
        return $this->belongsTo('App\ClassOfStudents', 'class_id', 'id');
    }

    public function delete()
    {
        $student_id = $this->student_id;
        $class_id = $this->class_id;
        ClassApplicationStudent::where('student_id', $student_id)
            ->whereHas('classApplication', function ($q) use ($class_id) {
                $q->where('class_id', $class_id);
            })->delete();

        // delete tests progress
        StudentTestQuestion::where('student_id', $student_id)
            ->whereHas('classApplication', function ($q) use ($class_id) {
                $q->where('class_id', $class_id);
            })->delete();
        StudentTestAttempt::whereHas('testStudent', function ($q1) use ($student_id, $class_id) {
            $q1->where('student_id', $student_id)->whereHas('classApplication', function ($q2) use ($class_id) {
                $q2->where('class_id', $class_id);
            });
        })->delete();

        // delete assignments progress
        DB::table('class_detailed_reports')
            ->where('class_id', $class_id)
            ->where('student_id', $student_id)
            ->delete();
        StudentsTrackingQuestion::where('student_id', $student_id)
            ->whereHas('classApplication', function ($q) use ($class_id) {
                $q->where('class_id', $class_id);
            })->delete();
        // progresses table not related to class
        // students_testout_attempts table not related to class

        return parent::delete();
    }

    public $timestamps = false;
}
