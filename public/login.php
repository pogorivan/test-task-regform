<?php
/**
 * @var $config array
 */

require 'include/config.php';
require 'include/Auth.php';
require 'include/User.php';

use TestTask\User;
use TestTask\Auth;

$dbh = new PDO($config['db_dsn'], $config['db_user'], $config['db_password']);

session_start();

if (Auth::isAuthenticated()){
    header("Location: index.php");
}

$loginError = false;
if (isset($_POST['email'])) {
    $errors = [];
    $emailVal = trim($_POST['email']);
    $passwordVal = trim($_POST['password']);
    if ($_REQUEST['reg']) {
        $nameVal = trim($_POST['name']);
        $passwordRepeatVal = trim($_POST['password_repeat']);
        $photoVal = '';
        if (empty($emailVal) || !filter_var($emailVal, FILTER_VALIDATE_EMAIL) || !preg_match('/@.+\./', $emailVal)) {
            $errors['regEmail'] = true;
        }
        if ($existedUser = User::findByEmail($dbh, $emailVal)) {
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

            $user->register($dbh);
            Auth::login($user);
            header("Location: index.php");
        }
    } else {
        if (empty($emailVal) || !filter_var($emailVal, FILTER_VALIDATE_EMAIL) || !preg_match('/@.+\./', $emailVal)) {
            $errors['loginEmail'] = true;
        }
        if (empty($passwordVal) || strlen($passwordVal) < 6) {
            $errors['loginPassword'] = true;
        }

        if (empty($errors)) {
            if ($user = User::findByEmailPassword($dbh, $emailVal, $passwordVal)) {
                Auth::login($user);
                header("Location: index.php");
            } else {
                $loginError = true;
            }
        }
    }
}

include 'include/header.php';
?>

<div class="login-form-cont">
    <h1 style="text-align: center">Для доступа к личному кабинету необходимо авторизоваться</h1>

    <div class="tabs-cont">
        <div class="tabs-navigation">
            <div id="tab-login-button" class="tab<?= $_REQUEST['reg'] ? '' : ' active' ?>"><h2>Войти</h2></div>
            <div id="tab-register-button" class="tab<?= $_REQUEST['reg'] ? ' active' : '' ?>"><h2>Зарегистрироваться</h2></div>
        </div>

        <div class="tabs-content">
            <div id="tab-login" class="tab<?= $_REQUEST['reg'] ? '' : ' active' ?>">
                <form method="post" action="?">
                    <div class="form-row<?= $errors['loginEmail'] ? ' has-error' : '' ?>">
                        <label for="login-form-email">Email</label>
                        <input id="login-form-email" type="text" name="email" value="<?= isset($emailVal) ? $emailVal : '' ?>">
                        <div class="error">Ввведите правильный email</div>
                    </div>
                    <div class="form-row<?= $errors['loginPassword'] ? ' has-error' : '' ?>">
                        <label for="login-form-password">Пароль</label>
                        <input id="login-form-password" type="password" name="password">
                        <div class="error">Введите пароль - минимум 6 символов</div>
                    </div>
                    <?= $loginError ? '<p id="login-error">Пользователь с таким email и паролем не найден</p>' : '' ?>
                    <button type="submit">Войти</button>
                </form>
            </div>
            <div id="tab-register" class="tab<?= $_REQUEST['reg'] ? ' active' : '' ?>">
                <form method="post" action="?reg=1" enctype="multipart/form-data">
                    <div class="form-row<?= $errors['regEmail'] ? ' has-error' : '' ?>">
                        <label for="register-form-email">Email <span class="red">*</span></label>
                        <input id="register-form-email" type="text" name="email" value="<?= isset($emailVal) ? $emailVal : '' ?>">
                        <div class="error">Ввведите правильный email</div>
                        <?= $errors['emailExists'] ? '<div id="email-exist">Данный email уже зарегистрирован</div>' : '' ?>
                    </div>
                    <div class="form-row<?= $errors['regName'] ? ' has-error' : '' ?>">
                        <label for="register-form-name">Имя <span class="red">*</span></label>
                        <input id="register-form-name" type="text" name="name" value="<?= isset($nameVal) ? $nameVal : '' ?>">
                        <div class="error">Ввведите имя</div>
                    </div>
                    <div class="form-row<?= $errors['regPhoto'] ? ' has-error' : '' ?>">
                        <label for="register-form-photo">Фото</label>
                        <input id="register-form-photo" type="file" name="photo">
                        <div class="error">Загрузите файл gif, jpg или png не более 8Мб.</div>
                    </div>
                    <div class="form-row<?= $errors['regPassword'] ? ' has-error' : '' ?>">
                        <label for="register-form-password">Пароль <span class="red">*</span></label>
                        <input id="register-form-password" type="password" name="password">
                        <div class="error">Введите пароль - минимум 6 символов</div>
                    </div>
                    <div class="form-row<?= $errors['regPasswordRepeat'] ? ' has-error' : '' ?>">
                        <label for="register-form-password-repeat">Повторите пароль <span class="red">*</span></label>
                        <input id="register-form-password-repeat" type="password" name="password_repeat">
                        <div class="error">Повтор пароля и пароль не совпадают</div>
                    </div>
                    <button type="submit">Войти</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'include/footer.php';