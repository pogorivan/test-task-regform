<?php

namespace App\Controllers;

use App\Components\Auth;
use App\Components\Controller;
use App\Models\User;

class Site extends Controller
{
    public function index()
    {
        if (!Auth::isAuthenticated()) {
            header("Location: /site/login");
            exit();
        }

        $userId = (int)$_SESSION['userId'];
        if (!$user = User::findById($userId)) {
            Auth::logout();
            header("Location: /site/login");
            exit();
        }

        return $this->render('home', ['user' => $user]);
    }

    public function login()
    {
        if (Auth::isAuthenticated()) {
            header("Location: /");
            exit();
        }

        $errors = [];
        $loginError = false;
        if (isset($_POST['email'])) {
            $emailVal = trim($_POST['email']);
            $passwordVal = trim($_POST['password']);
            if ($_REQUEST['reg']) {
                $nameVal = trim($_POST['name']);
                $passwordRepeatVal = trim($_POST['password_repeat']);
                $photoVal = '';
                if (empty($emailVal) || !filter_var($emailVal, FILTER_VALIDATE_EMAIL) || !preg_match('/@.+\./', $emailVal)) {
                    $errors['regEmail'] = true;
                }
                if ($existedUser = User::findByEmail($emailVal)) {
                    $errors['emailExists'] = true;
                }
                if (empty($nameVal)) {
                    $errors['regName'] = true;
                }
                if (empty($passwordVal) || strlen($passwordVal) < 6) {
                    $errors['regPassword'] = true;
                }
                if ($passwordRepeatVal != $passwordVal) {
                    $errors['regPasswordRepeat'] = true;
                }

                if (empty($errors) && !empty($_FILES['photo'] && !empty($_FILES['photo']['tmp_name']))) {
                    if (filesize($_FILES['photo']['tmp_name']) > 8 * 1024 * 1024 || !in_array($_FILES['photo']['type'], ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])) {
                        $errors['regPhoto'] = true;
                    } else {
                        $photoVal = $_FILES['photo'];
                    }
                }

                if (empty($errors)) {
                    $user = new User();
                    $user->email = $emailVal;
                    $user->name = $nameVal;
                    $user->photo = $photoVal;
                    $user->password = $passwordVal;

                    $user->register();
                    Auth::login($user);
                    header("Location: /");
                    exit();
                }
            } else {
                if (empty($emailVal) || !filter_var($emailVal, FILTER_VALIDATE_EMAIL) || !preg_match('/@.+\./', $emailVal)) {
                    $errors['loginEmail'] = true;
                }
                if (empty($passwordVal) || strlen($passwordVal) < 6) {
                    $errors['loginPassword'] = true;
                }

                if (empty($errors)) {
                    if ($user = User::findByEmailPassword($emailVal, $passwordVal)) {
                        Auth::login($user);
                        header("Location: /");
                        exit();
                    } else {
                        $loginError = true;
                    }
                }
            }
        }

        $this->render('login', [
            'regTab' => $_REQUEST['reg'] == 1,
            'errors' => $errors,
            'loginError' => $loginError,
            'formValues' => [
                'email' => $emailVal,
                'name' => $nameVal
            ]
        ]);
    }

    public function logout()
    {
        Auth::logout();
        header("Location: /site/login");
    }

    public function changelang()
    {
        $_SESSION['lang'] = $_GET['lang'];
        header("Location: ".$_SERVER['HTTP_REFERER']);
    }
}