<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Galleries\Response;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Pagination\MockRenderPaginationForTesting;
use App\Http\Response\Media\Galleries\GalleryResults;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class PaginatedGalleriesListResponderFactoryTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockRenderPaginationForTesting;
    use MockResponseFactoryForTesting;

    private PaginatedGalleriesListResponderFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new PaginatedGalleriesListResponderFactory(
            twig: $this->mockTwig(),
            heroFactory: $this->mockHeroFactory(),
            renderPagination: $this->mockRenderPagination(),
            responseFactory: $this->mockResponseFactory(),
        );
    }

    public function testWhenNoEntries(): void
    {
        $results = new GalleryResults(
            hasEntries: false,
            totalResults: 0,
            incomingItems: [],
        );

        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $responder = $this->factory->make(
            galleryResults: $results,
            request: $request,
        );

        self::assertInstanceOf(
            RespondWithNotFound::class,
            $responder,
        );
    }

    public function testWhenHasEntries(): void
    {
        $results = new GalleryResults(
            hasEntries: true,
            totalResults: 0,
            incomingItems: [],
        );

        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $responder = $this->factory->make(
            galleryResults: $results,
            request: $request,
        );

        self::assertInstanceOf(
            RespondWithResults::class,
            $responder,
        );
    }
}
