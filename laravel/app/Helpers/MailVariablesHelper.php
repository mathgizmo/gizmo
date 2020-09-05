<?php

namespace App\Helpers;

use App\ClassOfStudents;
use App\Student;

class MailVariablesHelper {

    public $studentVariablesKeys = array(
        'studentName',
        'studentFirstName',
        'studentLastName',
    );

    public $classVariablesKeys = array(
        'teacherName',
        'teacherFirstName',
        'teacherLastName',
        'className',
    );

    public function getStudentVariablesKeys() {
        return $this->studentVariablesKeys;
    }

    public function getClassVariablesKeys() {
        return $this->classVariablesKeys;
    }

    public function getStudentVariables(Student $user) {
        return array(
            'studentName' => $user->name,
            'studentFirstName' => $user->first_name,
            'studentLastName' => $user->last_name,
        );
    }

    public function getClassVariables(ClassOfStudents $class = null) {
        return array(
            'teacherName' => $class && $class->teacher ? $class->teacher->name : '',
            'teacherFirstName' => $class && $class->teacher ? $class->teacher->first_name : '',
            'teacherLastName' => $class && $class->teacher ? $class->teacher->last_name : '',
            'className' => $class ? $class->name : '',
        );
    }

}
