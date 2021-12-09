<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsItem;

use App\Http\Components\Hero\Hero;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class DisplayNewsItemResponderTest extends TestCase
{
    use MockTwigForTesting;
    use MockResponseFactoryForTesting;

    private DisplayNewsItemResponder $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = new DisplayNewsItemResponder(
            twig: $this->mockTwig(),
            responseFactory: $this->mockResponseFactory(),
        );
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testRespond(): void
    {
        $meta = new Meta();

        $hero = $this->createMock(Hero::class);

        $breadcrumbs = [
            new Link(
                isEmpty: false,
                content: 'test content',
                href: '/test-href',
                newWindow: false,
            ),
        ];

        $contentString = 'test content string';

        $response = $this->responder->respond(
            meta: $meta,
            hero: $hero,
            breadcrumbs: $breadcrumbs,
            contentString: $contentString,
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
                'object' => 'ResponseInterface',
                'method' => 'withHeader',
                'args' => [
                    'EnableStaticCache',
                    'true',
                ],
            ],
            $this->calls[1],
        );

        self::assertSame(
            [
                'object' => 'TwigEnvironment',
                'method' => 'render',
                'args' => [
                    'Http/_Infrastructure/Breadcrumbs.twig',
                    ['breadcrumbs' => $breadcrumbs],
                ],
            ],
            $this->calls[2]
        );

        self::assertSame(
            [
                'object' => 'TwigEnvironment',
                'method' => 'render',
            ],
            [
                'object' => $this->calls[3]['object'],
                'method' => $this->calls[3]['method'],
            ]
        );

        $args = $this->calls[3]['args'];

        self::assertCount(2, $args);

        self::assertSame(
            '@app/Http/Response/Pages/RenderPage/Standard/Page.twig',
            $args[0],
        );

        $context = $args[1];

        self::assertCount(4, $context);

        self::assertSame($meta, $context['meta']);

        self::assertSame($hero, $context['hero']);

        self::assertSame(
            'TwigRender',
            (string) $context['breadcrumbs'],
        );

        self::assertSame(
            'test content string',
            (string) $context['content'],
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
