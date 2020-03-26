<?php
namespace App\Components;

class Translate {

    const MESSAGES = [
        'en' => [],
        'ru' => [
            'Language' => 'Язык',
            'Test task' => 'Тестовое задание',
            'Logout' => 'Выйти',
            'To access your personal account you need to log in' => 'Для доступа к личному кабинету необходимо авторизоваться',
            'Log in' => 'Войти',
            'Register' => 'Зарегистрироваться',
            'Please enter correct email' => 'Ввведите правильный email',
            'Please enter password - at least 6 characters' => 'Введите пароль - минимум 6 символов',
            'User with such email and password was not found' => 'Пользователь с таким email и паролем не найден',
            'This email is already registered' => 'Данный email уже зарегистрирован',
            'Name' => 'Имя',
            'Please enter your name' => 'Ввведите имя',
            'Photo' => 'Фото',
            'Select gif, jpg or png picture no more than 8Mb' => 'Загрузите файл gif, jpg или png не более 8Мб',
            'Password' => 'Пароль',
            'Repeat password' => 'Повторите пароль',
            'Password repeat and password do not match' => 'Повтор пароля и пароль не совпадают',
            'User profile' => 'Профиль пользователя'
        ]
    ];

    /**
     * @var string
     */
    public $lang;

    /**
     * Translate constructor.
     */
    public function __construct()
    {
        $this->lang = 'en';
        if (isset($_SESSION['lang']) && array_key_exists($_SESSION['lang'], self::MESSAGES)) {
            $this->lang = $_SESSION['lang'];
        } else {
            $this->lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $this->lang = array_key_exists($this->lang, self::MESSAGES) ? $this->lang : 'en';
        }
    }

    /**
     * @param $message string
     * @return string
     */
    public function translate($message) {
        return $this->lang == 'en' ? $message : self::MESSAGES[$this->lang][$message];
    }
}