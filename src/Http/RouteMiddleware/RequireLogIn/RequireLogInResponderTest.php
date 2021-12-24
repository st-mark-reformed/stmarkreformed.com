<?php

declare(strict_types=1);

namespace App\Http\RouteMiddleware\RequireLogIn;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Entities\Meta;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use craft\errors\InvalidFieldException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

use function assert;

class RequireLogInResponderTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockResponseFactoryForTesting;

    private RequireLogInResponder $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = new RequireLogInResponder(
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
        $response = $this->responder->respond(
            pageTitle: 'Foo Bar Page Title',
            redirectTo: '/foo/bar/redirect',
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
                    'Foo Bar Page Title',
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
            '@app/Http/RouteMiddleware/RequireLogIn/RequireLogIn.twig',
            $call2Args[0],
        );

        $context = $call2Args[1];

        self::assertCount(3, $context);

        $meta = $context['meta'];

        assert($meta instanceof Meta);

        self::assertSame(
            'Foo Bar Page Title',
            $meta->metaTitle(),
        );

        self::assertSame($this->hero, $context['hero']);

        self::assertSame(
            '/foo/bar/redirect',
            $context['redirectTo'],
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
