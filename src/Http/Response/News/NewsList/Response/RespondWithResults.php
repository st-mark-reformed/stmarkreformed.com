<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsList\Response;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Entities\Meta;
use App\Http\Pagination\Pagination;
use App\Http\Pagination\RenderPagination;
use App\Http\Response\News\NewsList\NewsResults;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

class RespondWithResults implements PaginatedNewsListResponderContract
{
    public function __construct(
        private TwigEnvironment $twig,
        private HeroFactory $heroFactory,
        private RenderPagination $renderPagination,
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
        NewsResults $results,
        Pagination $pagination,
        string $pageTitle,
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twig->render(
            '@app/Http/Response/News/NewsList/Response/RespondWithResults.twig',
            [
                'meta' => new Meta(metaTitle: $pageTitle),
                'hero' => $this->heroFactory->createFromDefaults(
                    heroHeading: $pageTitle,
                ),
                'results' => $results,
                'pagination' => $this->renderPagination->render(
                    pagination: $pagination,
                ),
            ],
        ));

        return $response;
    }
}
