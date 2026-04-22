<?php

declare(strict_types=1);

/**
 * Laravel merges vendor/laravel/framework/config/database.php before app config;
 * that file still references PDO::MYSQL_ATTR_SSL_CA, which triggers a deprecation on PHP 8.5+.
 * This idempotent patch rewrites those option keys until the framework ships a fix for 11.x.
 */
$path = dirname(__DIR__).'/vendor/laravel/framework/config/database.php';

if (! is_file($path)) {
    exit(0);
}

$contents = file_get_contents($path);
if ($contents === false) {
    exit(0);
}

$needle = "PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),";
$replacement = '(defined(\'Pdo\\Mysql::ATTR_SSL_CA\') ? \Pdo\Mysql::ATTR_SSL_CA : \PDO::MYSQL_ATTR_SSL_CA) => env(\'MYSQL_ATTR_SSL_CA\'),';

if (! str_contains($contents, $needle)) {
    exit(0);
}

file_put_contents($path, str_replace($needle, $replacement, $contents));
