<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Response;

use App\Messages\RetrieveMessages\MessagesResult;

class ResponderFactory
{
    public function __construct(
        private RespondWithResults $respondWithResults,
        private RespondWithNoResults $respondWithNoResults,
    ) {
    }

    public function make(MessagesResult $result): ResponderContract
    {
        if ($result->count() > 0) {
            return $this->respondWithResults;
        }

        return $this->respondWithNoResults;
    }
}
