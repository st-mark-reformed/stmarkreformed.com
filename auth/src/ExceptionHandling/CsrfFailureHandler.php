<?php

declare(strict_types=1);

namespace App\ExceptionHandling;

use App\TemplateEngineFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class CsrfFailureHandler implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private TemplateEngineFactory $templateEngineFactory,
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse(
            400,
        );

        $response->getBody()->write(
            $this->templateEngineFactory->create()
                ->templatePath(
                    __DIR__ . '/HtmlExceptionResponse.phtml',
                )
                ->addVar('pageTitle', 'An Error Occurred')
                ->addVar('errorCode', '')
                ->addVar(
                    'errorDescription',
                    'Your request could not be validated.',
                )
                ->render(),
        );

        return $response;
    }
}
