<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia\Response;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use App\Http\Pagination\Pagination;
use App\Http\Response\Members\InternalMedia\MediaResults;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

class RespondWithNoResults implements InternalMediaResponderContract
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
        MediaResults $results,
        Pagination $pagination,
    ): ResponseInterface {
        $breadcrumbs = [
            new Link(
                isEmpty: false,
                content: 'Home',
                href: '/',
            ),
            new Link(
                isEmpty: false,
                content: 'Members',
                href: '/members',
            ),
            new Link(
                isEmpty: false,
                content: 'Internal Media',
            ),
        ];

        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twig->render(
            '@app/Http/Response/Members/InternalMedia/Response/RespondWithNoResults.twig',
            [
                'breadcrumbs' => $breadcrumbs,
                'meta' => new Meta(metaTitle: 'Internal Media'),
                'hero' => $this->heroFactory->createFromDefaults(
                    heroHeading: 'Internal Media',
                ),
            ],
        ));

        return $response;
    }
}
