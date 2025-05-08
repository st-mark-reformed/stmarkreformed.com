<?php

declare(strict_types=1);

namespace App\Http\Response\LogIn\Response;

use App\Http\Response\LogIn\LogInPayload;
use Psr\Http\Message\ResponseInterface;

interface LogInResponderContract
{
    public function respond(
        string $redirectTo,
        LogInPayload $payload,
    ): ResponseInterface;
}
