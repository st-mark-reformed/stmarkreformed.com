<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resources\Response;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Entities\Meta;
use App\Http\Pagination\MockRenderPaginationForTesting;
use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Resources\ResourceResults;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use craft\errors\InvalidFieldException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

use function assert;

class RespondWithResultsTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockResponseFactoryForTesting;
    use MockRenderPaginationForTesting;

    private RespondWithResults $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = new RespondWithResults(
            twig: $this->mockTwig(),
            heroFactory: $this->mockHeroFactory(),
            responseFactory: $this->mockResponseFactory(),
            renderPagination: $this->mockRenderPagination(),
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

        $results = new ResourceResults(
            hasEntries: false,
            totalResults: 0,
            incomingItems: [],
        );

        $response = $this->responder->respond(
            pagination: $pagination,
            results: $results,
        );

        self::assertSame($this->response, $response);

        self::assertCount(5, $this->calls);

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
                    'Resources',
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

        $call3Args = $this->calls[3]['args'];

        self::assertCount(2, $call3Args);

        self::assertSame(
            '@app/Http/Response/Media/Resources/Response/RespondWithResults.twig',
            $call3Args[0],
        );

        $call3Context = $call3Args[1];

        self::assertCount(4, $call3Context);

        $call3Meta = $call3Context['meta'];

        assert($call3Meta instanceof Meta);

        self::assertSame(
            'Resources',
            $call3Meta->metaTitle(),
        );

        self::assertSame(
            $this->hero,
            $call3Context['hero'],
        );

        self::assertSame(
            $results,
            $call3Context['results'],
        );

        self::assertSame(
            'RenderPaginationReturn',
            (string) $call3Context['pagination'],
        );

        self::assertSame(
            [
                'object' => 'StreamInterface',
                'method' => 'write',
                'args' => ['TwigRender'],
            ],
            $this->calls[4],
        );
    }
}
