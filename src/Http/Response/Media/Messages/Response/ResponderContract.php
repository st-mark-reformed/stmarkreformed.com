<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Response;

use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Messages\Params;
use App\Messages\RetrieveMessages\MessagesResult;
use Psr\Http\Message\ResponseInterface;

interface ResponderContract
{
    public function respond(
        Params $params,
        MessagesResult $result,
        Pagination $pagination,
    ): ResponseInterface;
}
