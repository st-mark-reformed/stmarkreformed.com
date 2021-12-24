<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Galleries\Response;

use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Galleries\GalleryResults;
use Psr\Http\Message\ResponseInterface;

interface PaginatedGalleriesListResponderContract
{
    public function respond(
        Pagination $pagination,
        GalleryResults $galleryResults,
    ): ResponseInterface;
}
