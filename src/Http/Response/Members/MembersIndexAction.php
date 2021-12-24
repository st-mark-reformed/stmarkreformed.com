<?php

declare(strict_types=1);

namespace App\Http\Response\Members;

use App\Http\RouteMiddleware\RequireLogIn\RequireLogInMiddleware;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

class MembersIndexAction
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector
            ->get(
                '/members',
                self::class,
            )
            ->setArgument(
                'pageTitle',
                'Log in to view the members area'
            )
            ->add(RequireLogInMiddleware::class);
    }

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function __invoke(): ResponseInterface
    {
        return $this->responseFactory->createResponse(303)
            ->withHeader('Location', '/members/internal-media');
    }
}
