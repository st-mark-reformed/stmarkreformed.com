<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia\Response;

use App\Http\Pagination\Pagination;
use App\Http\Response\Members\InternalMedia\MediaResults;
use Psr\Http\Message\ResponseInterface;

interface InternalMediaResponderContract
{
    public function respond(
        MediaResults $results,
        Pagination $pagination,
    ): ResponseInterface;
}
