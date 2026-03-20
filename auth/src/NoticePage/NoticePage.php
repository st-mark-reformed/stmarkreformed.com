<?php

declare(strict_types=1);

namespace App\NoticePage;

use App\Html\ButtonRows;
use App\TemplateEngineFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

readonly class NoticePage
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private TemplateEngineFactory $templateEngineFactory,
    ) {
    }

    public function generateHttpResponse(
        string $pageTitle,
        string|null $message = null,
        ButtonRows $buttonRows = new ButtonRows(),
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write(
            $this->templateEngineFactory->create()
                ->templatePath(__DIR__ . '/NoticePage.phtml')
                ->addVar('pageTitle', $pageTitle)
                ->addVar('message', $message)
                ->addVar('buttonRows', $buttonRows)
                ->render(),
        );

        return $response;
    }
}
