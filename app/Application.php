<?php

namespace App;

use App\Draft\Repository\DraftRepository;
use App\Draft\Repository\LocalDraftRepository;
use App\Draft\Repository\S3DraftRepository;
use App\Http\ErrorResponse;
use App\Http\HttpRequest;
use App\Http\HttpResponse;
use App\Http\RequestHandler;
use App\Http\Route;
use App\Http\RouteMatch;
use App\Shared\Command;
use App\Testing\DispatcherSpy;

/**
 * Unsure why I did this from scratch. I was on a bit of a refactoring roll and I couldn't resist.
 */
class Application
{
    public readonly DraftRepository $repository;
    private static self $instance;

    public bool $spyOnDispatcher = false;
    public DispatcherSpy $spy;

    public function __construct()
    {
        if (env('STORAGE', ' local') == 'spaces') {
            $this->repository = new S3DraftRepository();
        } else {
            $this->repository = new LocalDraftRepository();
        }
    }

    public function run()
    {
        $response = $this->handleIncomingRequest();

        http_response_code($response->code);
        header('Content-type: ' . $response->getContentType());
        echo $response->getBody();
        exit;
    }

    private function handleIncomingRequest(): HttpResponse
    {
        try {
            $handler = $this->handlerForRequest($_SERVER['REQUEST_URI']);
            if ($handler == null) {
                return new ErrorResponse("Page not found", 404, true);
            } else {
                return $handler->handle();
            }

        } catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }
    }

    private function matchToRoute(string $path): ?RouteMatch
    {
        $routes = include 'app/routes.php';

        foreach($routes as $route => $handlerClass) {
            $route = new Route($route, $handlerClass);
            $match = $route->match($path);

            if ($match != null) {
                return $match;
            }
        }

        return null;
    }

    public function handlerForRequest(string $requestUri): ?RequestHandler
    {
        $requestChunks = explode("?", $requestUri);

        $match = $this->matchToRoute($requestChunks[0]);

        if ($match == null) {
            return null;
        } else {
            $request = HttpRequest::fromRequest($match->requestParameters);

            $handler = new $match->requestHandlerClass($request);

            if (!$handler instanceof RequestHandler) {
                throw new \Exception("Handler does not implement RequestHandler");
            }

            return $handler;
        }
    }

    public function spyOnDispatcher($commandReturnValue = null)
    {
        $this->spyOnDispatcher = true;
        $this->spy = new DispatcherSpy($commandReturnValue);
    }

    public function dontSpyOnDispatcher()
    {
        $this->spyOnDispatcher = false;
        unset($this->spy);
    }

    public function handle(Command $command)
    {
        if ($this->spyOnDispatcher) {
            return $this->spy->handle($command);
        } else {
            return $command->handle();
        }
    }

    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new Application();
        }

        return self::$instance;
    }
}