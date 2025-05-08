<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth\Response;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use App\Http\Response\Members\HymnsOfTheMonth\HymnResults;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use craft\errors\InvalidFieldException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

use function array_map;
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
        $results = new HymnResults(hasResults: true);

        $response = $this->responder->respond(results: $results);

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
                    'Hymns of the Month',
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

        $args = $this->calls[2]['args'];

        self::assertCount(2, $args);

        self::assertSame(
            '@app/Http/Response/Members/HymnsOfTheMonth/Response/RespondWithNoResults.twig',
            $args[0],
        );

        $context = $args[1];

        self::assertCount(3, $context);

        self::assertSame(
            [
                [
                    'isEmpty' => false,
                    'content' => 'Home',
                    'href' => '/',
                    'newWindow' => false,
                ],
                [
                    'isEmpty' => false,
                    'content' => 'Members',
                    'href' => '/members',
                    'newWindow' => false,
                ],
                [
                    'isEmpty' => false,
                    'content' => 'Hymns of the Month',
                    'href' => '',
                    'newWindow' => false,
                ],
            ],
            array_map(
                static fn (Link $link) => [
                    'isEmpty' => $link->isEmpty(),
                    'content' => $link->content(),
                    'href' => $link->href(),
                    'newWindow' => $link->newWindow(),
                ],
                $context['breadcrumbs'],
            ),
        );

        $meta = $context['meta'];

        assert($meta instanceof Meta);

        self::assertSame(
            'Hymns of the Month',
            $meta->metaTitle(),
        );

        self::assertSame($this->hero, $context['hero']);

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
