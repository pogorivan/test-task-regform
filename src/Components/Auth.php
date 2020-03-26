<?php
namespace App\Components;

use App\Models\User;

class Auth {
    /**
     * @param User $user
     */
    public static function login(User $user)
    {
        $_SESSION['userId'] = $user->id;
    }

    public static function logout()
    {
        unset($_SESSION['userId']);
    }

    /**
     * @return bool
     */
    public static function isAuthenticated()
    {
        return isset($_SESSION['userId']);
    }
}