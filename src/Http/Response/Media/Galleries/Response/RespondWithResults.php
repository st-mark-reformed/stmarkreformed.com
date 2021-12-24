<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Galleries\Response;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Entities\Meta;
use App\Http\Pagination\Pagination;
use App\Http\Pagination\RenderPagination;
use App\Http\Response\Media\Galleries\GalleryResults;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

class RespondWithResults implements PaginatedGalleriesListResponderContract
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
        Pagination $pagination,
        GalleryResults $galleryResults,
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twig->render(
            '@app/Http/Response/Media/Galleries/Response/RespondWithResults.twig',
            [
                'meta' => new Meta(metaTitle: 'Photo Galleries'),
                'hero' => $this->heroFactory->createFromDefaults(
                    heroHeading: 'Photo Galleries',
                ),
                'galleryResults' => $galleryResults,
                'pagination' => $this->renderPagination->render(
                    pagination: $pagination,
                ),
            ],
        ));

        return $response;
    }
}
