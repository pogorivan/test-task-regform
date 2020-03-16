<?php
use TestTask\Auth;
?>

<html lang="ru">
<head>
    <title>Тестовое задание</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="top-menu">
        <?= Auth::isAuthenticated() ? '<a href="/logout.php">Выход</a>' : '' ?>
    </div>