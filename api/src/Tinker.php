<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

use function json_encode;

readonly class Tinker
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->any('/tinker', self::class);
    }

    public function __construct()
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response->getBody()->write((string) json_encode(
            ['status' => 'OK'],
        ));

        return $response->withHeader('Content-type', 'application/json');
    }
}
