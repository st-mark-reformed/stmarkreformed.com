<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Galleries\Response;

use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Galleries\GalleryResults;
use App\Shared\Testing\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

use function assert;

class RespondWithNotFoundTest extends TestCase
{
    private ServerRequestInterface $request;

    private RespondWithNotFound $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = $this->createMock(
            ServerRequestInterface::class,
        );

        $this->responder = new RespondWithNotFound(request: $this->request);
    }

    public function testRespond(): void
    {
        $exception = null;

        $pagination = new Pagination();

        $results = new GalleryResults(
            hasEntries: true,
            totalResults: 123,
            incomingItems: [],
        );

        try {
            $this->responder->respond(
                pagination: $pagination,
                galleryResults: $results,
            );
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof HttpNotFoundException);

        self::assertSame(
            $this->request,
            $exception->getRequest(),
        );
    }
}
