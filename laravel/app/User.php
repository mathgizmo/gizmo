<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'role'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function isQuestionsEditor() {
        return $this->role == 'questions_editor';
    }

    public function isAdmin() {
        return $this->role == 'admin' || $this->role == 'superadmin';
    }

    public function isSuperAdmin() {
        return $this->role == 'superadmin';
    }

    public function roleName() {
        switch ($this->role) {
            default:
            case 'questions_editor':
                return 'Questions Editor';
            case 'admin':
                return 'Admin';
            case 'superadmin':
                return 'Super Admin';
        }
    }
}
