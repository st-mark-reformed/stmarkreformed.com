<?php

declare(strict_types=1);

namespace App\Authentication;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class RequireCmsAccessRoleMiddleware extends RequireRoleMiddleware
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        return $this->processInternal(
            $request,
            $handler,
            Role::CMS_USER,
        );
    }
}
