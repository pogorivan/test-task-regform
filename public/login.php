<?php
/**
 * @var $config array
 */

session_start();

require 'include/config.php';
require 'include/Auth.php';
require 'include/User.php';
require 'include/Translate.php';

use TestTask\User;
use TestTask\Auth;
use TestTask\Translate;

$dbh = new PDO($config['db_dsn'], $config['db_user'], $config['db_password']);

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

$translator = new Translate();

include 'include/header.php';
?>

<div class="login-form-cont">
    <h1 style="text-align: center"><?= $translator->translate('To access your personal account you need to log in') ?></h1>

    <div class="tabs-cont">
        <div class="tabs-navigation">
            <div id="tab-login-button" class="tab<?= $_REQUEST['reg'] ? '' : ' active' ?>"><h2><?= $translator->translate('Log in') ?></h2></div>
            <div id="tab-register-button" class="tab<?= $_REQUEST['reg'] ? ' active' : '' ?>"><h2><?= $translator->translate('Register') ?></h2></div>
        </div>

        <div class="tabs-content">
            <div id="tab-login" class="tab<?= $_REQUEST['reg'] ? '' : ' active' ?>">
                <form method="post" action="?">
                    <div class="form-row<?= $errors['loginEmail'] ? ' has-error' : '' ?>">
                        <label for="login-form-email">Email</label>
                        <input id="login-form-email" type="text" name="email" value="<?= isset($emailVal) ? $emailVal : '' ?>">
                        <div class="error"><?= $translator->translate('Please enter correct email') ?></div>
                    </div>
                    <div class="form-row<?= $errors['loginPassword'] ? ' has-error' : '' ?>">
                        <label for="login-form-password"><?= $translator->translate('Password') ?></label>
                        <input id="login-form-password" type="password" name="password">
                        <div class="error"><?= $translator->translate('Please enter password - at least 6 characters') ?></div>
                    </div>
                    <?= $loginError ? '<p id="login-error">'.$translator->translate('User with such email and password was not found').'</p>' : '' ?>
                    <button type="submit"><?= $translator->translate('Log in') ?></button>
                </form>
            </div>
            <div id="tab-register" class="tab<?= $_REQUEST['reg'] ? ' active' : '' ?>">
                <form method="post" action="?reg=1" enctype="multipart/form-data">
                    <div class="form-row<?= $errors['regEmail'] ? ' has-error' : '' ?>">
                        <label for="register-form-email">Email <span class="red">*</span></label>
                        <input id="register-form-email" type="text" name="email" value="<?= isset($emailVal) ? $emailVal : '' ?>">
                        <div class="error"><?= $translator->translate('Please enter correct email') ?></div>
                        <?= $errors['emailExists'] ? '<div id="email-exist">'.$translator->translate('This email is already registered').'</div>' : '' ?>
                    </div>
                    <div class="form-row<?= $errors['regName'] ? ' has-error' : '' ?>">
                        <label for="register-form-name"><?= $translator->translate('Name') ?> <span class="red">*</span></label>
                        <input id="register-form-name" type="text" name="name" value="<?= isset($nameVal) ? $nameVal : '' ?>">
                        <div class="error"><?= $translator->translate('Please enter your name') ?></div>
                    </div>
                    <div class="form-row<?= $errors['regPhoto'] ? ' has-error' : '' ?>">
                        <label for="register-form-photo"><?= $translator->translate('Photo') ?></label>
                        <input id="register-form-photo" type="file" name="photo">
                        <div class="error"><?= $translator->translate('Select gif, jpg or png picture no more than 8Mb') ?></div>
                    </div>
                    <div class="form-row<?= $errors['regPassword'] ? ' has-error' : '' ?>">
                        <label for="register-form-password"><?= $translator->translate('Password') ?> <span class="red">*</span></label>
                        <input id="register-form-password" type="password" name="password">
                        <div class="error"><?= $translator->translate('Please enter password - at least 6 characters') ?></div>
                    </div>
                    <div class="form-row<?= $errors['regPasswordRepeat'] ? ' has-error' : '' ?>">
                        <label for="register-form-password-repeat"><?= $translator->translate('Repeat password') ?> <span class="red">*</span></label>
                        <input id="register-form-password-repeat" type="password" name="password_repeat">
                        <div class="error"><?= $translator->translate('Password repeat and password do not match') ?></div>
                    </div>
                    <button type="submit"><?= $translator->translate('Register') ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'include/footer.php';