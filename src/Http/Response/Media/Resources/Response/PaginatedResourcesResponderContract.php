<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resources\Response;

use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Resources\ResourceResults;
use Psr\Http\Message\ResponseInterface;

interface PaginatedResourcesResponderContract
{
    public function respond(
        Pagination $pagination,
        ResourceResults $results,
    ): ResponseInterface;
}
