<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsList\Response;

use App\Http\Pagination\Pagination;
use App\Http\Response\News\NewsList\NewsResults;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class RespondWithNotFound implements PaginatedNewsListResponderContract
{
    public function __construct(private ServerRequestInterface $request)
    {
    }

    /**
     * @throws HttpNotFoundException
     */
    public function respond(
        NewsResults $results,
        Pagination $pagination,
        string $pageTitle,
    ): ResponseInterface {
        throw new HttpNotFoundException($this->request);
    }
}
