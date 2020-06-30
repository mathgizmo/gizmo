<?php

namespace App\Policies;

use App\User;
use App\MailTemplate;
use Illuminate\Auth\Access\HandlesAuthorization;

class MailTemplatePolicy
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

    public function update(User $user, MailTemplate $mailTemplate)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function delete(User $user, MailTemplate $mailTemplate)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }
}
