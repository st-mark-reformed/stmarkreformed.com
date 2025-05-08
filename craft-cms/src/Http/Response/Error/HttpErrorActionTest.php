<?php

declare(strict_types=1);

namespace App\Http\Response\Error;

use App\Shared\Testing\TestCase;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

class HttpErrorActionTest extends TestCase
{
    private ResponseInterface $error404Response;

    private ResponseInterface $error500Response;

    private ServerRequestInterface $request;

    private HttpErrorAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->error404Response = $this->createMock(
            ResponseInterface::class,
        );

        $error404Responder = $this->createMock(
            Error404Responder::class,
        );

        $error404Responder->method('respond')->willReturn(
            $this->error404Response,
        );

        $this->error500Response = $this->createMock(
            ResponseInterface::class,
        );

        $error500Responder = $this->createMock(
            Error500Responder::class,
        );

        $error500Responder->method('respond')->willReturn(
            $this->error500Response,
        );

        $this->request = $this->createMock(
            ServerRequestInterface::class,
        );

        $this->action = new HttpErrorAction(
            error404Responder: $error404Responder,
            error500Responder: $error500Responder
        );
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testWhenInstanceOfNotFound(): void
    {
        $exception = $this->createMock(
            HttpNotFoundException::class,
        );

        $response = ($this->action)(
            request: $this->request,
            exception: $exception,
        );

        self::assertSame(
            $this->error404Response,
            $response,
        );
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testWhenOtherException(): void
    {
        $exception = $this->createMock(
            Throwable::class,
        );

        $response = ($this->action)(
            request: $this->request,
            exception: $exception,
        );

        self::assertSame(
            $this->error500Response,
            $response,
        );
    }
}
