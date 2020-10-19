<?php

namespace App\Policies;

use App\User;
use App\ReportError;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportErrorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isQuestionsEditor();
    }

    public function view(User $user)
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isQuestionsEditor();
    }

    public function create(User $user)
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isQuestionsEditor();
    }

    public function update(User $user, ReportError $model)
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isQuestionsEditor();
    }

    public function delete(User $user, ReportError $model)
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isQuestionsEditor();
    }
}
