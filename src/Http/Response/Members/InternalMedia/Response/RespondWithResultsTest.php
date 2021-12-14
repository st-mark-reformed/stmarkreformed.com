<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia\Response;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Components\Link\Link;
use App\Http\PageBuilder\Shared\AudioPlayer\MockAudioPlayerContentModelFactoryForTesting;
use App\Http\PageBuilder\Shared\AudioPlayer\MockRenderAudioPlayerFromContentModelForTesting;
use App\Http\Pagination\MockRenderPaginationForTesting;
use App\Http\Pagination\Pagination;
use App\Http\Response\Members\InternalMedia\MediaResults;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

use function array_map;

class RespondWithResultsTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockResponseFactoryForTesting;
    use MockRenderPaginationForTesting;
    use MockAudioPlayerContentModelFactoryForTesting;
    use MockRenderAudioPlayerFromContentModelForTesting;

    private RespondWithResults $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = new RespondWithResults(
            twig: $this->mockTwig(),
            heroFactory: $this->mockHeroFactory(),
            responseFactory: $this->mockResponseFactory(),
            renderPagination: $this->mockRenderPagination(),
            playerModelFactory: $this->mockAudioPlayerContentModelFactory(),
            renderAudioPlayer: $this->mockRenderAudioPlayerFromContentModel(),
        );
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testRespond(): void
    {
        $entry1 = $this->createMock(Entry::class);

        $entry2 = $this->createMock(Entry::class);

        $results = new MediaResults(
            hasEntries: false,
            totalResults: 0,
            incomingEntries: [$entry1, $entry2],
        );

        $pagination = new Pagination();

        $response = $this->responder->respond(
            results: $results,
            pagination: $pagination,
        );

        self::assertSame($this->response, $response);

        self::assertCount(7, $this->calls);

        self::assertSame(
            [
                'object' => 'ResponseFactoryInterface',
                'method' => 'createResponse',
                'args' => [],
            ],
            $this->calls[0],
        );

        self::assertSame(
            [
                'object' => 'HeroFactory',
                'method' => 'createFromDefaults',
                'args' => [
                    0,
                    '',
                    null,
                    'Internal Media',
                ],
            ],
            $this->calls[1],
        );

        self::assertSame(
            [
                'object' => 'RenderPagination',
                'method' => 'render',
                'args' => [$pagination],
            ],
            $this->calls[2],
        );

        self::assertSame(
            [
                'object' => 'AudioPlayerContentModelFactory',
                'method' => 'makeFromInternalMessageEntry',
                'args' => [$entry1],
            ],
            $this->calls[3],
        );

        self::assertSame(
            [
                'object' => 'AudioPlayerContentModelFactory',
                'method' => 'makeFromInternalMessageEntry',
                'args' => [$entry2],
            ],
            $this->calls[4],
        );

        self::assertSame(
            'TwigEnvironment',
            $this->calls[5]['object'],
        );

        self::assertSame(
            'render',
            $this->calls[5]['method'],
        );

        $args = $this->calls[5]['args'];

        self::assertCount(2, $args);

        self::assertSame(
            '@app/Http/Response/Members/InternalMedia/Response/RespondWithResults.twig',
            $args[0],
        );

        $context = $args[1];

        self::assertCount(5, $context);

        self::assertSame(
            [
                [
                    'newWindow' => false,
                    'href' => '/',
                    'content' => 'Home',
                    'isEmpty' => false,
                ],
                [
                    'newWindow' => false,
                    'href' => '/members',
                    'content' => 'Members',
                    'isEmpty' => false,
                ],
                [
                    'newWindow' => false,
                    'href' => '',
                    'content' => 'Internal Media',
                    'isEmpty' => false,
                ],
            ],
            array_map(
                static fn (Link $link) => [
                    'newWindow' => $link->newWindow(),
                    'href' => $link->href(),
                    'content' => $link->content(),
                    'isEmpty' => $link->isEmpty(),
                ],
                $context['breadcrumbs'],
            ),
        );

        self::assertSame(
            'Internal Media',
            $context['meta']->metaTitle(),
        );

        self::assertSame($this->hero, $context['hero']);

        self::assertSame(
            'RenderPaginationReturn',
            (string) $context['pagination'],
        );

        $audioPlayers = $context['audioPlayers'];

        self::assertCount(2, $audioPlayers);

        self::assertSame(
            'RenderAudioPlayerFromContentModelReturnString',
            (string) $audioPlayers[0],
        );

        self::assertSame(
            'RenderAudioPlayerFromContentModelReturnString',
            (string) $audioPlayers[1],
        );

        self::assertSame(
            [
                'object' => 'StreamInterface',
                'method' => 'write',
                'args' => ['TwigRender'],
            ],
            $this->calls[6],
        );
    }
}
