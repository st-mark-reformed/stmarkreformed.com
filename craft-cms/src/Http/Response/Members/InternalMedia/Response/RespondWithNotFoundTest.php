<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia\Response;

use App\Http\Pagination\Pagination;
use App\Http\Response\Members\InternalMedia\MediaResults;
use App\Shared\Testing\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

use function assert;

class RespondWithNotFoundTest extends TestCase
{
    public function testRespond(): void
    {
        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $responder = new RespondWithNotFound(request: $request);

        $exception = null;

        try {
            $responder->respond(
                results: new MediaResults(
                    hasEntries: false,
                    totalResults: 0,
                    incomingEntries: [],
                ),
                pagination: new Pagination(),
            );
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof HttpNotFoundException);

        self::assertSame($request, $exception->getRequest());
    }
}
