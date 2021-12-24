<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Galleries\Response;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Entities\Meta;
use App\Http\Pagination\MockRenderPaginationForTesting;
use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Galleries\GalleryResults;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use craft\errors\InvalidFieldException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

use function assert;
use function is_array;

class RespondWithResultsTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockRenderPaginationForTesting;
    use MockResponseFactoryForTesting;

    private RespondWithResults $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = new RespondWithResults(
            twig: $this->mockTwig(),
            heroFactory: $this->mockHeroFactory(),
            renderPagination: $this->mockRenderPagination(),
            responseFactory: $this->mockResponseFactory(),
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
        $pagination = new Pagination();

        $results = new GalleryResults(
            hasEntries: true,
            totalResults: 123,
            incomingItems: [],
        );

        $response = $this->responder->respond(
            pagination: $pagination,
            galleryResults: $results,
        );

        self::assertSame($this->response, $response);

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
                    'Photo Galleries',
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
            'TwigEnvironment',
            $this->calls[3]['object'],
        );

        self::assertSame(
            'render',
            $this->calls[3]['method'],
        );

        self::assertSame(
            'render',
            $this->calls[3]['method'],
        );

        $call3Args = $this->calls[3]['args'];

        assert(is_array($call3Args));

        self::assertCount(2, $call3Args);

        self::assertSame(
            '@app/Http/Response/Media/Galleries/Response/RespondWithResults.twig',
            $call3Args[0],
        );

        $twigArgs = $call3Args[1];

        self::assertCount(4, $twigArgs);

        $meta = $twigArgs['meta'];

        assert($meta instanceof Meta);

        self::assertSame(
            'Photo Galleries',
            $meta->metaTitle(),
        );

        self::assertSame($this->hero, $twigArgs['hero']);

        self::assertSame(
            $results,
            $twigArgs['galleryResults'],
        );

        self::assertSame(
            'RenderPaginationReturn',
            (string) $twigArgs['pagination'],
        );

        self::assertSame(
            [
                'object' => 'StreamInterface',
                'method' => 'write',
                'args' => [0 => 'TwigRender'],
            ],
            $this->calls[4],
        );
    }
}
