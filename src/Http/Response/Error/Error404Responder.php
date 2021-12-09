<?php

declare(strict_types=1);

namespace App\Http\Response\Error;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Entities\Meta;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

class Error404Responder
{
    public function __construct(
        private TwigEnvironment $twig,
        private HeroFactory $heroFactory,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function respond(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(
            404,
            'Page not found',
        );

        $response->getBody()->write(
            $this->twig->render(
                '@app/Http/Response/Error/Error404.twig',
                [
                    'meta' => new Meta(
                        metaTitle: 'Page Not Found',
                    ),
                    'hero' => $this->heroFactory->createFromDefaults(),
                ],
            ),
        );

        return $response;
    }
}
