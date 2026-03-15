<?php

declare(strict_types=1);

namespace App\ExceptionHandling;

use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RxAnte\AppBootstrap\Http\IsJsonRequest;

readonly class OauthRequestExceptionHandlerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private IsJsonRequest $isJsonRequest,
        private ResponseFactoryInterface $responseFactory,
        private HtmlExceptionResponse $htmlExceptionResponse,
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (OAuthServerException $exception) {
            if (! $this->isJsonRequest->checkRequest($request)) {
                return $this->htmlExceptionResponse->generateHttpResponse(
                    $exception,
                );
            }

            return $exception->generateHttpResponse(
                $this->responseFactory->createResponse(),
            );
        }
    }
}
