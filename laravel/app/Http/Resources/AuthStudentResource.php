<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthStudentResource extends JsonResource
{

    public function toArray($request)
    {
        $role = 'student';
        if ($this->is_self_study) {
            $role = 'self_study';
        }
        if ($this->is_teacher) {
            $role = 'teacher';
        }
        $student = [
            'user_id' => $this->id,
            'username' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'role' => $role,
            'country_id' => $this->country_id,
            'options' => [
                'is_test_timer_displayed' => $this->is_test_timer_displayed,
                'is_test_questions_count_displayed' => $this->is_test_questions_count_displayed
            ]
        ];
        return $student;
    }
}
