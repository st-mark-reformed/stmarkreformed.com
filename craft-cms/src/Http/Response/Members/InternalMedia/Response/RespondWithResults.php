<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia\Response;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use App\Http\PageBuilder\Shared\AudioPlayer\AudioPlayerContentModel;
use App\Http\PageBuilder\Shared\AudioPlayer\AudioPlayerContentModelFactory;
use App\Http\PageBuilder\Shared\AudioPlayer\RenderAudioPlayerFromContentModel;
use App\Http\Pagination\Pagination;
use App\Http\Pagination\RenderPagination;
use App\Http\Response\Members\InternalMedia\MediaResults;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;
use yii\base\InvalidConfigException;

use function array_map;

class RespondWithResults implements InternalMediaResponderContract
{
    public function __construct(
        private TwigEnvironment $twig,
        private HeroFactory $heroFactory,
        private RenderPagination $renderPagination,
        private ResponseFactoryInterface $responseFactory,
        private AudioPlayerContentModelFactory $playerModelFactory,
        private RenderAudioPlayerFromContentModel $renderAudioPlayer,
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
            '@app/Http/Response/Members/InternalMedia/Response/RespondWithResults.twig',
            [
                'breadcrumbs' => $breadcrumbs,
                'meta' => new Meta(metaTitle: 'Internal Media'),
                'hero' => $this->heroFactory->createFromDefaults(
                    heroHeading: 'Internal Media',
                ),
                'pagination' => $this->renderPagination->render(
                    pagination: $pagination,
                ),
                'audioPlayers' => array_map(
                    function (
                        AudioPlayerContentModel $model,
                    ): Markup {
                        return new Markup(
                            $this->renderAudioPlayer->render(
                                contentModel: $model,
                            ),
                            'UTF-8',
                        );
                    },
                    $results->mapItems([
                        $this->playerModelFactory,
                        'makeFromInternalMessageEntry',
                    ]),
                ),
            ],
        ));

        return $response;
    }
}
