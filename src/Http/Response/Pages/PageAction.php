<?php

declare(strict_types=1);

namespace App\Http\Response\Pages;

use App\Http\Response\Pages\RenderPage\RenderPageFactory;
use App\Http\Shared\RouteParamsHandler;
use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use yii\base\InvalidConfigException;

class PageAction
{
    public function __construct(
        private RouteParams $routeParams,
        private RenderPageFactory $renderPageFactory,
        private RouteParamsHandler $routeParamsHandler,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function __invoke(): ResponseInterface
    {
        $entry = $this->routeParamsHandler->getEntry(
            routeParams: $this->routeParams
        );

        $response = $this->responseFactory->createResponse()
            ->withHeader('EnableStaticCache', 'true');

        $response->getBody()->write(
            $this->renderPageFactory->make(entry: $entry)->render(),
        );

        return $response;
    }
}
