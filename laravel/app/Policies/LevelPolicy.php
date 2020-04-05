<?php

namespace App\Policies;

use App\User;
use App\Level;
use Illuminate\Auth\Access\HandlesAuthorization;

class LevelPolicy
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

    public function update(User $user, Level $model)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function delete(User $user, Level $model)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }
}
