<?php

declare(strict_types=1);

namespace App\Http\Response\Pages;

use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use craft\elements\Entry;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

use function assert;

class PageAction
{
    public function __construct(
        private RouteParams $routeParams,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function __invoke(): ResponseInterface
    {
        // TODO: Implement this page action

        $entry = $this->routeParams->getParam('element');

        assert($entry instanceof Entry);

        $response = $this->responseFactory->createResponse();

        $response->getBody()->write('title: ' . (string) $entry->title);

        return $response;
    }
}
