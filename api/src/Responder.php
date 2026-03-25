<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface;

interface Responder
{
    public function respond(): ResponseInterface;
}
