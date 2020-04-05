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

    public function update(User $user, ReportError $model)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function delete(User $user, ReportError $model)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }
}
