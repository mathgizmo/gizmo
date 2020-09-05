<?php

namespace App\Policies;

use App\User;
use App\Question;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionPolicy
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

    public function update(User $user, Question $question)
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isQuestionsEditor();
    }

    public function delete(User $user, Question $question)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }
}
