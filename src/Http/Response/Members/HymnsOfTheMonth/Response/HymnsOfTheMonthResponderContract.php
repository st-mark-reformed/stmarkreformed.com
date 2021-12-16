<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth\Response;

use App\Http\Response\Members\HymnsOfTheMonth\HymnResults;
use Psr\Http\Message\ResponseInterface;

interface HymnsOfTheMonthResponderContract
{
    public function respond(HymnResults $results): ResponseInterface;
}
