<?php
/**
 * @var $config array
 */

require 'include/config.php';

$dbh = new PDO($config['db_dsn'], $config['db_user'], $config['db_password']);

$dbh->query("CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `photo` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

print_r($dbh->errorInfo());