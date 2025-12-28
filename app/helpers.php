<?php

declare(strict_types=1);

if (! function_exists('d')) {
    function d(...$variables): void
    {
        echo '<pre>';
        foreach($variables as $v) {
            var_dump($v);
        }
        echo '</pre>';
    }
}

if (! function_exists('app')) {
    function app():App\Application
    {
        return App\Application::getInstance();
    }
}

if (! function_exists('dispatch')) {
    function dispatch(App\Shared\Command $command): mixed
    {
        return app()->handle($command);
    }
}

if (! function_exists('dd')) {
    function dd(...$variables): void
    {
        echo '<pre>';
        foreach ($variables as $var) {
            var_dump($var);
        }
        die('</pre>');
    }
}

if (! function_exists('e')) {
    function e($condition, $yes, $no = ''): void
    {
        if ($condition) echo $yes;
        else echo $no;
    }
}

if (! function_exists('yesno')) {
    /**
     * return "yes" or "no" based on condition
     *
     * @param $condition
     * @return string
     */
    function yesno($condition): string
    {
        return $condition ? 'yes' : 'no';
    }
}

if (! function_exists('env')) {
    function env($key, $defaultValue = null)
    {
        return $_ENV[$key] ?? $defaultValue;
    }
}

if (! function_exists('human_filesize')) {
    function human_filesize($bytes, $dec = 2): string {

        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = (int) floor((strlen($bytes) - 1) / 3);
        if ($factor == 0) $dec = 0;

        return sprintf("%.{$dec}f %s", $bytes / (1024 ** $factor), $size[$factor]);
    }
}

if (! function_exists('get')) {
    function get($key, $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }
}

if (! function_exists('url')) {
    function url($uri): string
    {
        return env('URL', 'https://milty.shenanigans.be/') . $uri;
    }
}

if (! function_exists('asset_url')) {
    function asset_url($uri): string
    {
        return url($uri . '?v=' . (env('DEBUG', false) ? (string) time() : env('VERSION')));
    }
}

if (! function_exists('ordinal')) {
    function ordinal($number): string
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if ((($number % 100) >= 11) && (($number % 100) <= 13))
            return $number . 'th';
        else
            return $number . $ends[$number % 10];
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
