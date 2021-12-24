<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsList\Response;

use App\Http\Pagination\Pagination;
use App\Http\Response\News\NewsList\NewsResults;
use Psr\Http\Message\ResponseInterface;

interface PaginatedNewsListResponderContract
{
    public function respond(
        NewsResults $results,
        Pagination $pagination,
        string $pageTitle,
    ): ResponseInterface;
}
