<?php

declare(strict_types=1);

namespace App\Http\Response\Pages;

use App\Http\Response\Media\Messages\GenerateMessagesPagesForRedis;
use App\Http\Response\Pages\RenderPage\RenderPageFactory;
use App\Http\Shared\RouteParamsHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use yii\base\InvalidConfigException;

class PageAction
{
    public function __construct(
        private RouteParams $routeParams,
        private GenericHandler $genericHandler,
        private RenderPageFactory $renderPageFactory,
        private RouteParamsHandler $routeParamsHandler,
        private ResponseFactoryInterface $responseFactory,
        private GenerateMessagesPagesForRedis $generateMessagesPagesForRedis,
    ) {
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function __invoke(): ResponseInterface
    {
        $this->generateMessagesPagesForRedis->generate();
        $entry = $this->routeParamsHandler->getEntry(
            routeParams: $this->routeParams
        );

        $enableStaticCache = $this->genericHandler->getBoolean(
            element: $entry,
            field: 'enableStaticCache',
        );

        $response = $this->responseFactory->createResponse();

        if ($enableStaticCache) {
            $response = $response->withHeader(
                'EnableStaticCache',
                'true'
            );
        }

        $response->getBody()->write(
            $this->renderPageFactory->make(entry: $entry)->render(),
        );

        return $response;
    }
}
