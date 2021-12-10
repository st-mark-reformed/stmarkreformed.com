<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsList\Response;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Entities\Meta;
use App\Http\Pagination\Pagination;
use App\Http\Response\News\NewsList\NewsResults;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use craft\errors\InvalidFieldException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

use function assert;

class RespondWithNoResultsTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockResponseFactoryForTesting;

    private RespondWithNoResults $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = new RespondWithNoResults(
            twig: $this->mockTwig(),
            heroFactory: $this->mockHeroFactory(),
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

        $results = new NewsResults(
            hasEntries: false,
            totalResults: 0,
            incomingItems: [],
        );

        $response = $this->responder->respond(
            pagination: $pagination,
            results: $results,
            pageTitle: 'Test Title',
        );

        self::assertSame($this->response, $response);

        self::assertCount(4, $this->calls);

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
                    'Test Title',
                ],
            ],
            $this->calls[1],
        );

        self::assertSame(
            'TwigEnvironment',
            $this->calls[2]['object'],
        );

        self::assertSame(
            'render',
            $this->calls[2]['method'],
        );

        $call2Args = $this->calls[2]['args'];

        self::assertCount(2, $call2Args);

        self::assertSame(
            '@app/Http/Response/News/NewsList/Response/RespondWithNoResults.twig',
            $call2Args[0],
        );

        $call2Context = $call2Args[1];

        self::assertCount(2, $call2Context);

        $call2Meta = $call2Context['meta'];

        assert($call2Meta instanceof Meta);

        self::assertSame(
            'Test Title',
            $call2Meta->metaTitle(),
        );

        self::assertSame(
            $this->hero,
            $call2Context['hero'],
        );

        self::assertSame(
            [
                'object' => 'StreamInterface',
                'method' => 'write',
                'args' => ['TwigRender'],
            ],
            $this->calls[3],
        );
    }
}
