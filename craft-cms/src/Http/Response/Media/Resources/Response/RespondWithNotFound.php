<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resources\Response;

use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Resources\ResourceResults;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class RespondWithNotFound implements PaginatedResourcesResponderContract
{
    public function __construct(private ServerRequestInterface $request)
    {
    }

    /**
     * @throws HttpNotFoundException
     */
    public function respond(
        Pagination $pagination,
        ResourceResults $results,
    ): ResponseInterface {
        throw new HttpNotFoundException($this->request);
    }
}
