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

if (!Auth::isAuthenticated()){
    header("Location: login.php");
    exit();
}

$userId = (int)$_SESSION['userId'];
if (!$user = User::findById($dbh, $userId)) {
    Auth::logout();
    header("Location: login.php");
    exit();
}

include "include/header.php";
?>

<div class="user-profile">
    <h1>Профиль пользователя</h1>

    <table>
        <tr>
            <td>Имя:</td>
            <td><?= $user->name ?></td>
        </tr>
        <tr>
            <td>Email:</td>
            <td><?= $user->email ?></td>
        </tr>
        <?php if (!empty($user->photo)) : ?>
        <tr>
            <td>Фото:</td>
            <td><img src="/uploads/<?= $user->photo ?>" alt="" style="max-width: 500px;max-height: 500px"></td>
        </tr>
        <?php endif; ?>
    </table>
</div>
<?php

include "include/footer.php";