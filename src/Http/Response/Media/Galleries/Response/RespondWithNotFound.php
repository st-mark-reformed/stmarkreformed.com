<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Galleries\Response;

use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Galleries\GalleryResults;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class RespondWithNotFound implements PaginatedGalleriesListResponderContract
{
    public function __construct(private ServerRequestInterface $request)
    {
    }

    /**
     * @throws HttpNotFoundException
     */
    public function respond(
        Pagination $pagination,
        GalleryResults $galleryResults,
    ): ResponseInterface {
        throw new HttpNotFoundException($this->request);
    }
}
