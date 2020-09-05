<?php

namespace App\Policies;

use App\User;
use App\Lesson;
use Illuminate\Auth\Access\HandlesAuthorization;

class LessonPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function view(User $user)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function create(User $user)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function update(User $user, Lesson $lesson)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function delete(User $user, Lesson $lesson)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }
}
