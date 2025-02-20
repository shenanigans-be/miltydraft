<?php

function d($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

function dd(...$variables)
{
    echo '<pre>';
    foreach($variables as $var) {
        var_dump($var);
    }
    die('</pre>');
}

function e($condition, $yes, $no = '')
{
    if ($condition) echo $yes;
    else echo $no;
}

function get($param, $default = null)
{
    if (isset($_POST[$param])) return $_POST[$param];
    if (isset($_GET[$param])) return $_GET[$param];
    return $default;
}

function url($uri)
{
    return $_ENV['URL'] . $uri;
}

function return_error($err)
{
    die(json_encode(['error' => $err]));
}

function return_data($data)
{
    header('Access-Control-Allow-Origin: *');
    die(json_encode($data));
}

function ordinal($number)
{
    $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if ((($number % 100) >= 11) && (($number % 100) <= 13))
        return $number . 'th';
    else
        return $number . $ends[$number % 10];
}

function abort($code, $message = null) {
    http_response_code($code);
    switch($code) {
        case 404:
            die($message ?? 'Not found');
        default:
            if(!$_ENV['DEBUG']) {
                die('Something went wrong');
            } else {
                die($message ?? 'Something went wrong');
            }
    }
}