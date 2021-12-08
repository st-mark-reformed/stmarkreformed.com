<?php

declare(strict_types=1);

namespace App\Http\Shared;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

use function assert;
use function is_string;

class PageNumberFactory
{
    /**
     * @throws HttpNotFoundException
     */
    public function fromRequest(ServerRequestInterface $request): int
    {
        $pageNum = $request->getAttribute('pageNum');

        assert($pageNum === null || is_string($pageNum));

        if ($pageNum === '1') {
            throw new HttpNotFoundException($request);
        }

        if ($pageNum === null) {
            $pageNum = 1;
        }

        return (int) $pageNum;
    }
}
