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
            $role = $this->is_researcher ? 'researcher' : 'teacher';
        }
        $student = [
            'user_id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'email_new' => $this->email_new ?: null,
            'role' => $role,
            'country_id' => $this->country_id,
            'app_id' => $this->app_id,
            'options' => [
                'is_test_timer_displayed' => $this->is_test_timer_displayed,
                'is_test_questions_count_displayed' => $this->is_test_questions_count_displayed
            ]
        ];
        return $student;
    }
}
