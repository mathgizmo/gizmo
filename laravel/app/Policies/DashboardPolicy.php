<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardPolicy
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

    public function update(User $user, User $model)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function delete(User $user, User $model)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }
}
