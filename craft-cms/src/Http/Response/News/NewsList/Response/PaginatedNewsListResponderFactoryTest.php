<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsList\Response;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Pagination\MockRenderPaginationForTesting;
use App\Http\Pagination\Pagination;
use App\Http\Response\News\NewsList\NewsResults;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class PaginatedNewsListResponderFactoryTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockRenderPaginationForTesting;
    use MockResponseFactoryForTesting;

    private PaginatedNewsListResponderFactory $factory;

    private Pagination $pagination;

    private ServerRequestInterface $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new PaginatedNewsListResponderFactory(
            twig: $this->mockTwig(),
            heroFactory: $this->mockHeroFactory(),
            renderPagination: $this->mockRenderPagination(),
            responseFactory: $this->mockResponseFactory(),
        );

        $this->pagination = new Pagination();

        $this->request = $this->createMock(
            ServerRequestInterface::class,
        );
    }

    public function testWhenNoEntriesAndPageGreaterThanOne(): void
    {
        $results = new NewsResults(
            hasEntries: false,
            totalResults: 0,
            incomingItems: [],
        );

        $responder = $this->factory->make(
            results: $results,
            request: $this->request,
            pagination: $this->pagination->withCurrentPage(2),
        );

        self::assertInstanceOf(
            RespondWithNotFound::class,
            $responder,
        );
    }

    public function testWhenNoEntries(): void
    {
        $results = new NewsResults(
            hasEntries: false,
            totalResults: 0,
            incomingItems: [],
        );

        $responder = $this->factory->make(
            results: $results,
            request: $this->request,
            pagination: $this->pagination->withCurrentPage(1),
        );

        self::assertInstanceOf(
            RespondWithNoResults::class,
            $responder,
        );
    }

    public function testWhenHasEntries(): void
    {
        $results = new NewsResults(
            hasEntries: true,
            totalResults: 0,
            incomingItems: [],
        );

        $responder = $this->factory->make(
            results: $results,
            request: $this->request,
            pagination: $this->pagination->withCurrentPage(45),
        );

        self::assertInstanceOf(
            RespondWithResults::class,
            $responder,
        );
    }
}
