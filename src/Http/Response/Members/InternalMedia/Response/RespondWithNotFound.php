<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia\Response;

use App\Http\Pagination\Pagination;
use App\Http\Response\Members\InternalMedia\MediaResults;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class RespondWithNotFound implements InternalMediaResponderContract
{
    public function __construct(private ServerRequestInterface $request)
    {
    }

    /**
     * @throws HttpNotFoundException
     */
    public function respond(
        MediaResults $results,
        Pagination $pagination,
    ): ResponseInterface {
        throw new HttpNotFoundException($this->request);
    }
}
