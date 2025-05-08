<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia\Response;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\PageBuilder\Shared\AudioPlayer\MockAudioPlayerContentModelFactoryForTesting;
use App\Http\PageBuilder\Shared\AudioPlayer\MockRenderAudioPlayerFromContentModelForTesting;
use App\Http\Pagination\MockRenderPaginationForTesting;
use App\Http\Pagination\Pagination;
use App\Http\Response\Members\InternalMedia\MediaResults;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class InternalMediaResponderFactoryTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockResponseFactoryForTesting;
    use MockRenderPaginationForTesting;
    use MockAudioPlayerContentModelFactoryForTesting;
    use MockRenderAudioPlayerFromContentModelForTesting;

    private ServerRequestInterface $request;

    private InternalMediaResponderFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = $this->createMock(
            ServerRequestInterface::class,
        );

        $this->factory = new InternalMediaResponderFactory(
            twig: $this->mockTwig(),
            heroFactory: $this->mockHeroFactory(),
            responseFactory: $this->mockResponseFactory(),
            renderPagination: $this->mockRenderPagination(),
            playerModelFactory: $this->mockAudioPlayerContentModelFactory(),
            renderAudioPlayer: $this->mockRenderAudioPlayerFromContentModel(),
        );
    }

    public function testMakeWhenNoEntriesAndPageGreaterThanOne(): void
    {
        $responder = $this->factory->make(
            request: $this->request,
            results: new MediaResults(
                totalResults: 0,
                hasEntries: false,
                incomingEntries: [],
            ),
            pagination: (new Pagination())->withCurrentPage(3),
        );

        self::assertInstanceOf(
            RespondWithNotFound::class,
            $responder,
        );
    }

    public function testMakeWhenNoEntries(): void
    {
        $responder = $this->factory->make(
            request: $this->request,
            results: new MediaResults(
                totalResults: 0,
                hasEntries: false,
                incomingEntries: [],
            ),
            pagination: (new Pagination())->withCurrentPage(1),
        );

        self::assertInstanceOf(
            RespondWithNoResults::class,
            $responder,
        );
    }

    public function testMake(): void
    {
        $responder = $this->factory->make(
            request: $this->request,
            results: new MediaResults(
                totalResults: 0,
                hasEntries: true,
                incomingEntries: [],
            ),
            pagination: (new Pagination())->withCurrentPage(1),
        );

        self::assertInstanceOf(
            RespondWithResults::class,
            $responder,
        );
    }
}
