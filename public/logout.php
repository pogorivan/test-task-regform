<?php
/**
 * @var $config array
 */

require 'include/config.php';
require 'include/Auth.php';

use TestTask\Auth;

session_start();
Auth::logout();
header("Location: login.php");