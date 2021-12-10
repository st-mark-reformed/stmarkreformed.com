<?php

declare(strict_types=1);

namespace App\Http\Response\LogIn\Response;

use App\Http\Response\LogIn\LogInPayload;

class LogInResponderFactory
{
    public function __construct(
        private RespondWithError $respondWithError,
        private RespondWithSuccess $respondWithSuccess,
    ) {
    }

    public function make(LogInPayload $payload): LogInResponderContract
    {
        if ($payload->succeeded()) {
            return $this->respondWithSuccess;
        }

        return $this->respondWithError;
    }
}
