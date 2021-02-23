<?php

namespace App\Helpers;

use App\ClassOfStudents;
use App\Student;

class MailVariablesHelper {

    public $studentVariablesKeys = array(
        'studentEmail',
        'studentFirstName',
        'studentLastName',
    );

    public $classVariablesKeys = array(
        'teacherEmail',
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
            'studentEmail' => $user->email,
            'studentFirstName' => $user->first_name,
            'studentLastName' => $user->last_name,
        );
    }

    public function getClassVariables(ClassOfStudents $class = null) {
        return array(
            'teacherEmail' => $class && $class->teacher ? $class->teacher->email : '',
            'teacherFirstName' => $class && $class->teacher ? $class->teacher->first_name : '',
            'teacherLastName' => $class && $class->teacher ? $class->teacher->last_name : '',
            'className' => $class ? $class->name : '',
        );
    }

}
