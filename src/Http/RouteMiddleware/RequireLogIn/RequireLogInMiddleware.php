<?php

declare(strict_types=1);

namespace App\Http\RouteMiddleware\RequireLogIn;

use craft\errors\InvalidFieldException;
use craft\web\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

use function assert;

class RequireLogInMiddleware implements MiddlewareInterface
{
    public function __construct(
        private User $user,
        private RequireLogInResponder $responder,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        if ($this->user->getIsGuest()) {
            $route = $request->getAttribute('__route__');

            assert($route instanceof Route || $route === null);

            $pageTitle = $route?->getArgument('pageTitle');

            return $this->responder->respond(
                pageTitle: $pageTitle ?? 'Log In',
                redirectTo: $request->getUri()->getPath(),
            );
        }

        return $handler->handle($request);
    }
}
