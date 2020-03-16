<?php
/**
 * @var $translator \TestTask\Translate
 */

use TestTask\Auth;
?>

<html lang="ru">
<head>
    <title><?= $translator->translate('Test task') ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="top-menu">
        <b><?= $translator->translate('Lenguage') ?>:</b>
        <span class="languages">
            <?= $translator->lang == 'ru' ? '<b>Рус</b>' : '<a href="/changelang.php?lang=ru">Рус</a>' ?>
            <?= $translator->lang == 'en' ? '<b>Eng</b>' : '<a href="/changelang.php?lang=en">Eng</a>' ?>
        </span>
        <?= Auth::isAuthenticated() ? '<a href="/logout.php" class="logout-button">'.$translator->translate('Logout').'</a>' : '' ?>
    </div>