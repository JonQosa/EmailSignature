<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $loggedInUser, User $user)
    {
        return true;
    }

    public function update(User $loggedInUser, User $user)
    {
        // return $loggedInUser->id === $user->id;
        return $loggedInUser->isAdmin();
    }

    public function delete(User $loggedInUser, User $user)
    {
        return $loggedInUser->isAdmin(); // Example of admin check
    }
}
