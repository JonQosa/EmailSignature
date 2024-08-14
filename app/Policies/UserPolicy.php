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
//     public function view(User $user, Signature $signature)
// {
//     return $user->id === $signature->user_id;
// }

    public function view(User $loggedInUser, User $user)
    {
        return true;
    }

    // public function update(User $loggedInUser, User $user)
    // {
    //     // return $loggedInUser->id === $user->id;
    //     return $loggedInUser->isAdmin();
    // }

    public function update(User $user, User $model)
{
    // Admins can update any user; others can only update their own profile
    return $user->isAdmin() || $user->id === $model->id;
}

    public function delete(User $loggedInUser, User $user)
    {
        return $loggedInUser->isAdmin(); // Example of admin check
    }
}
