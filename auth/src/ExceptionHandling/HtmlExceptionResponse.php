<?php

declare(strict_types=1);

namespace App\ExceptionHandling;

use App\TemplateEngineFactory;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

readonly class HtmlExceptionResponse
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private TemplateEngineFactory $templateEngineFactory,
    ) {
    }

    public function generateHttpResponse(
        OAuthServerException $exception,
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse(
            $exception->getHttpStatusCode(),
        );

        $response->getBody()->write(
            $this->templateEngineFactory->create()
                ->templatePath(
                    __DIR__ . '/HtmlExceptionResponse.phtml',
                )
                ->addVar('pageTitle', 'An Error Occurred')
                ->addVar('errorCode', $exception->getHttpStatusCode())
                ->addVar('errorDescription', $exception->getMessage())
                ->render(),
        );

        return $response;
    }
}
