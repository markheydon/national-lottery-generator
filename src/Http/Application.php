<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Front-controller router for the application.
 */
final class Application
{
    public function handle(string $method, string $path): Response
    {
        if ($method !== 'GET') {
            return new Response('Method Not Allowed', 405);
        }

        $path = rtrim($path, '/') ?: '/';

        if ($path === '/') {
            return (new GameController())->index();
        }

        if (preg_match('#^/game/([a-z0-9-]+)/generate$#', $path, $matches) === 1) {
            return (new GameController())->generate($matches[1]);
        }

        return new Response('Not Found', 404);
    }

    /**
     * Handle the current PHP request environment.
     */
    public function handleRequest(): Response
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        return $this->handle($method, $path);
    }
}
