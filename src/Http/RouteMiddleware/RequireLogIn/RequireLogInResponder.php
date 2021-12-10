<?php

declare(strict_types=1);

namespace App\Http\RouteMiddleware\RequireLogIn;

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

class RequireLogInResponder
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
    public function respond(
        string $pageTitle,
        string $redirectTo,
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twig->render(
            '@app/Http/RouteMiddleware/RequireLogIn/RequireLogIn.twig',
            [
                'meta' => new Meta(
                    metaTitle: $pageTitle,
                ),
                'hero' => $this->heroFactory->createFromDefaults(
                    heroHeading: $pageTitle,
                ),
                'redirectTo' => $redirectTo,
            ]
        ));

        return $response;
    }
}
