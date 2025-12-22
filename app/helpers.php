<?php

if (!function_exists('d')) {
    function d($var)
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}


if (!function_exists('app')) {
    function app():\App\Application
    {
        return \App\Application::getInstance();
    }
}

if (!function_exists('dd')) {
    function dd(...$variables)
    {
        echo '<pre>';
        foreach ($variables as $var) {
            var_dump($var);
        }
        die('</pre>');
    }
}


if (!function_exists('e')) {
    function e($condition, $yes, $no = '')
    {
        if ($condition) echo $yes;
        else echo $no;
    }
}

if (!function_exists('env')) {
    function env($key, $defaultValue = null)
    {
        return $_ENV[$key] ?? $defaultValue;
    }
}

if (!function_exists('get')) {
    function get($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }
}

if (!function_exists('url')) {
    function url($uri)
    {
        return env('URL', 'https://milty.shenanigans.be/') . $uri;
    }
}


if (!function_exists('ordinal')) {
    function ordinal($number)
    {
        $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        if ((($number % 100) >= 11) && (($number % 100) <= 13))
            return $number . 'th';
        else
            return $number . $ends[$number % 10];
    }
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


if (! function_exists('class_uses_recursive')) {
    /**
     * Returns all traits used by a class, its parent classes and trait of their traits.
     * ("Borrowed" from Laravel)
     *
     * @param  object|string  $class
     * @return array
     */
    function class_uses_recursive($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $results = [];

        foreach (array_reverse(class_parents($class) ?: []) + [$class => $class] as $class) {
            $results += trait_uses_recursive($class);
        }

        return array_unique($results);
    }
}


if (! function_exists('trait_uses_recursive')) {
    /**
     * Returns all traits used by a trait and its traits.
     *("Borrowed" from Laravel)
     *
     * @param  object|string  $trait
     * @return array
     */
    function trait_uses_recursive($trait)
    {
        $traits = class_uses($trait) ?: [];

        foreach ($traits as $trait) {
            $traits += trait_uses_recursive($trait);
        }

        return $traits;
    }
}
