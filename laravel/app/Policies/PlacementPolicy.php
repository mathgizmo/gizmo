<?php

namespace App\Policies;

use App\User;
use App\PlacementQuestion as Placement;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlacementPolicy
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

    public function update(User $user, Placement $model)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function delete(User $user, Placement $model)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }
}
