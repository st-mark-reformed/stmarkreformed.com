<?php

declare(strict_types=1);

namespace App\Http\Response\Publications;

use Psr\Http\Message\ResponseInterface;

class DisplayPublicationEntryAction
{
    public function __invoke(): ResponseInterface
    {
        dd('here');
    }
}
