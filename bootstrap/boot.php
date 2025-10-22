<?php

if (!file_exists('vendor/autoload.php')) {
    die("<h2>No vendor directory found. Try running composer install.</h2>");
}

require_once 'vendor/autoload.php';
require_once 'bootstrap/helpers.php';

function app() {
    try {
        $requestPath = $_SERVER['REQUEST_URI'];

        $requestChunks = explode('?', $requestPath);

        if($requestChunks[0] == '/') {
            require_once 'templates/generate.php';
        } else {
            $pathChunks = explode('/', substr($requestChunks[0], 1));

            if(count($pathChunks) == 0) {
                throw new Exception("Something went wrong decoding path");
            }

            if($pathChunks[0] == 'd') {
                if(!isset($pathChunks[1])) {
                    abort(404, 'No draft specified');
                }

                define('DRAFT_ID', $pathChunks[1]);
                require_once 'templates/draft.php';
            } elseif ($pathChunks[0] == 'api') {

                if(!isset($pathChunks[1])) {
                    abort(404, 'No API endpoint specified');
                }

                $apiFile = __DIR__ . '/../app/api/' . $pathChunks[1] . '.php';

                if (file_exists($apiFile)) {
                    require_once $apiFile;
                } else {
                    abort(404, 'Unknown API endpoint');
                }
            } else {
                abort(404, 'Unknown path');
            }
        }
    } catch (Exception $e) {
        abort(500, $e->getMessage());
    }
}

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
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
