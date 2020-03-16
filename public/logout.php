<?php
/**
 * @var $config array
 */

session_start();

require 'include/config.php';
require 'include/Auth.php';

use TestTask\Auth;

Auth::logout();
header("Location: login.php");