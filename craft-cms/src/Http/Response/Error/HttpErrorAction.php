<?php

declare(strict_types=1);

namespace App\Http\Response\Error;

use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

class HttpErrorAction
{
    public function __construct(
        private Error404Responder $error404Responder,
        private Error500Responder $error500Responder,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
    ): ResponseInterface {
        if ($exception instanceof HttpNotFoundException) {
            return $this->error404Responder->respond();
        }

        return $this->error500Responder->respond(exception: $exception);
    }
}
