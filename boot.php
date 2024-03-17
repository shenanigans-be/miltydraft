<?php

if (!file_exists('vendor/autoload.php')) {
    die("<h2>No vendor directory found. Try running composer install.</h2>");
}

require_once 'vendor/autoload.php';
require_once 'helpers.php';

foreach (glob(__DIR__ . '/app/*.php') as $file) {
    require_once $file;
}

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} catch (Exception $e) {
    die("<h2>No .env file found. See .env.example or the readme for details</h2>");
}

if (!isset($_ENV['URL'])) {
    die("<h2>.env file is incomplete: no URL set.</h2>");
}

if (!isset($_ENV['STORAGE'])) {
    die("<h2>.env file is incomplete: no STORAGE set.</h2>");
}

if ($_ENV['STORAGE'] == "local") {
    if (!isset($_ENV['STORAGE_PATH'])) {
        die("<h2>.env file is incomplete: if STORAGE is local, then STORAGE_PATH is required.</h2>");
    } else {

        if (!is_writable($_ENV['STORAGE_PATH'])) {

            die("<h2>STORAGE_PATH does not exist or is not writeable.</h2>");
        }
    }
}
