<?php

declare(strict_types=1);

namespace App\Http;

class Route
{
    private array $routeChunks;

    public function __construct(
        private readonly string $route,
        private readonly string $handlerClass,
    ) {
        $this->routeChunks = explode('/', $this->route);
    }

    /**
     * @param string $path
     * @return ?RouteMatch
     */
    public function match(string $path): ?RouteMatch
    {
        $pathChunks = explode('/', $path);

        // remove trailing slash
        if ($path != '/' && $pathChunks[count($pathChunks) - 1] == '') {
            $pathChunks = array_slice($pathChunks, 0, count($pathChunks) - 1);
        }

        // if it's not a match on chunk size, already discard
        if (count($this->routeChunks) != count($pathChunks)) {
            return null;
        }

        $parameters = [];
        $allChunksMatch = true;
        foreach($this->routeChunks as $i => $chunk) {

            preg_match('/^\{(\w+)}$/', $chunk, $matches);
            if (count($matches) > 0) {
                $parameters[$matches[1]] = $pathChunks[$i];
            } else {
                if ($pathChunks[$i] != $chunk) {
                    $allChunksMatch = false;
                }
            }
        }

        if (! $allChunksMatch) {
            return null;
        }

        return new RouteMatch(
            $this->handlerClass,
            $parameters,
        );
    }
}