<?php

declare(strict_types=1);

namespace App\ExceptionHandling;

use App\TemplateEngineFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\IsJsonRequest;
use Slim\Error\Renderers\JsonErrorRenderer;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

readonly class SlimErrorHandler implements ErrorHandlerInterface
{
    public function __construct(
        private IsJsonRequest $isJsonRequest,
        private JsonErrorRenderer $jsonErrorRenderer,
        private ResponseFactoryInterface $responseFactory,
        private TemplateEngineFactory $templateEngineFactory,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails,
    ): ResponseInterface {
        $statusCode = $exception->getCode() === 404 ? 404 : 500;

        $response = $this->responseFactory->createResponse($statusCode);

        if ($exception instanceof KnownHandleableError) {
            $displayErrorDetails = true;
        }

        if ($this->isJsonRequest->checkRequest($request)) {
            $response->getBody()->write($this->jsonErrorRenderer->__invoke(
                $exception,
                $displayErrorDetails,
            ));

            return $response->withHeader(
                'Content-type',
                'application/json',
            );
        }

        $errorDescription = '';

        if ($statusCode === 404) {
            $errorDescription = 'We couldn\'t find the page you requested';
        } elseif ($displayErrorDetails) {
            $errorDescription = $exception->getMessage();
        }

        if ($errorDescription === '') {
            $errorDescription = 'We ran into a problem and could not display the page';
        }

        $response->getBody()->write(
            $this->templateEngineFactory->create()
                ->templatePath(
                    __DIR__ . '/HtmlExceptionResponse.phtml',
                )
                ->addVar(
                    'pageTitle',
                    $statusCode === 404  ?
                        'Page not found' :
                        'An Error Occurred',
                )
                ->addVar('errorCode', $statusCode)
                ->addVar('errorDescription', $errorDescription)
                ->render(),
        );

        return $response;
    }
}
