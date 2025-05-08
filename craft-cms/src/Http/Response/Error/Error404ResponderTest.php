<?php

declare(strict_types=1);

namespace App\Http\Response\Error;

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

class Error404ResponderTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockResponseFactoryForTesting;

    private Error404Responder $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = new Error404Responder(
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
        $response = $this->responder->respond();

        self::assertSame($this->response, $response);

        self::assertSame(
            [
                'object' => 'ResponseFactoryInterface',
                'method' => 'createResponse',
                'args' => [
                    404,
                    'Page not found',
                ],
            ],
            $this->calls[0],
        );

        self::assertSame(
            [
                'object' => 'HeroFactory',
                'method' => 'createFromDefaults',
                'args' => [],
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

        self::assertCount(
            2,
            $this->calls[2]['args'],
        );

        self::assertSame(
            '@app/Http/Response/Error/Error404.twig',
            $this->calls[2]['args'][0],
        );

        $context = $this->calls[2]['args'][1];

        self::assertCount(2, $context);

        assert($context['meta'] instanceof Meta);

        self::assertSame(
            'Page Not Found',
            $context['meta']->metaTitle(),
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
