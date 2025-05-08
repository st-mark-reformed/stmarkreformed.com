<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth\Response;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use App\Http\Response\Members\HymnsOfTheMonth\HymnResults;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

class RespondWithResults implements HymnsOfTheMonthResponderContract
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
    public function respond(HymnResults $results): ResponseInterface
    {
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
                content: 'Hymns of the Month',
            ),
        ];

        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twig->render(
            '@app/Http/Response/Members/HymnsOfTheMonth/Response/RespondWithResults.twig',
            [
                'results' => $results,
                'breadcrumbs' => $breadcrumbs,
                'meta' => new Meta(metaTitle: 'Hymns of the Month'),
                'hero' => $this->heroFactory->createFromDefaults(
                    heroHeading: 'Hymns of the Month',
                ),
            ],
        ));

        return $response;
    }
}
