<?php

declare(strict_types=1);

namespace App\Http\Response\LogIn;

use App\Http\Response\LogIn\Response\LogInResponderFactory;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

use function assert;
use function is_array;

class PostLogInAction
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->post('/log-in', self::class);
    }

    public function __construct(
        private CraftUserHandler $craftUserHandler,
        private LogInResponderFactory $responderFactory,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $postData = $request->getParsedBody();

        assert(is_array($postData));

        $email = (string) ($postData['email'] ?? '');

        $password = (string) ($postData['password'] ?? '');

        $redirectTo = (string) ($postData['redirect_to'] ?? '');

        $payload = $this->craftUserHandler->logUserIn(
            email: $email,
            password: $password,
        );

        return $this->responderFactory->make(payload: $payload)->respond(
            payload: $payload,
            redirectTo: $redirectTo,
        );
    }
}
