<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Response;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Entities\Meta;
use App\Http\PageBuilder\Shared\AudioPlayer\AudioPlayerContentModel;
use App\Http\PageBuilder\Shared\AudioPlayer\AudioPlayerContentModelFactory;
use App\Http\PageBuilder\Shared\AudioPlayer\RenderAudioPlayerFromContentModel;
use App\Http\Pagination\Pagination;
use App\Http\Pagination\RenderPagination;
use App\Http\Response\Media\Messages\Breadcrumbs\BreadcrumbBuilder;
use App\Http\Response\Media\Messages\Params;
use App\Http\Response\Media\Messages\SearchForm\SearchFormBuilder;
use App\Http\Response\Media\Messages\Sidebar\MessagesSidebar;
use App\Messages\RetrieveMessages\MessagesResult;
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

class RespondWithResults implements ResponderContract
{
    public function __construct(
        private TwigEnvironment $twig,
        private HeroFactory $heroFactory,
        private MessagesSidebar $messagesSidebar,
        private RenderPagination $renderPagination,
        private BreadcrumbBuilder $breadcrumbBuilder,
        private SearchFormBuilder $searchFormBuilder,
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
        Params $params,
        MessagesResult $result,
        Pagination $pagination,
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twig->render(
            '@app/Http/Response/Media/Messages/Response/RespondWithResults.twig',
            [
                'hero' => $this->heroFactory->createFromDefaults(
                    heroHeading: 'Messages from St. Mark',
                ),
                'meta' => new Meta(metaTitle: 'Messages from St. Mark'),
                'sideBarMarkup' => $this->messagesSidebar->render(),
                'breadcrumbs' => $this->breadcrumbBuilder->fromParams(
                    params: $params,
                ),
                'searchForm' => $this->searchFormBuilder->fromParams(
                    params: $params
                ),
                'pagination' => $this->renderPagination->render(
                    pagination: $pagination,
                ),
                'audioPlayers' => array_map(
                    function (AudioPlayerContentModel $model): Markup {
                        return new Markup(
                            $this->renderAudioPlayer->render(
                                contentModel: $model,
                            ),
                            'UTF-8',
                        );
                    },
                    $result->map([
                        $this->playerModelFactory,
                        'makeFromSermonEntry',
                    ]),
                ),
            ],
        ));

        return $response;
    }
}
