<?php

declare(strict_types=1);

namespace App\Http\Response\Error;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Entities\Meta;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Throwable;

use function assert;

class Error500ResponderTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockResponseFactoryForTesting;

    private Throwable $exception;

    private Error500Responder $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exception = $this->createMock(
            Throwable::class,
        );

        $this->responder = new Error500Responder(
            twig: $this->mockTwig(),
            logger: $this->mockLoggerInterface(),
            heroFactory: $this->mockHeroFactory(),
            responseFactory: $this->mockResponseFactory(),
        );
    }

    /**
     * @return MockObject&LoggerInterface
     */
    private function mockLoggerInterface(): MockObject|LoggerInterface
    {
        $logger = $this->createMock(LoggerInterface::class);

        $logger->method(self::anything())->willReturnCallback(
            function (): void {
                $this->genericCall(object: 'LoggerInterface');
            }
        );

        return $logger;
    }

    public function testRespond(): void
    {
        $response = $this->responder->respond(
            exception: $this->exception,
        );

        self::assertSame($this->response, $response);

        self::assertSame(
            [
                'object' => 'LoggerInterface',
                'method' => 'error',
                'args' => [
                    'An exception was thrown',
                    ['exception' => $this->exception],
                ],
            ],
            $this->calls[0],
        );

        self::assertSame(
            [
                'object' => 'ResponseFactoryInterface',
                'method' => 'createResponse',
                'args' => [
                    500,
                    'An internal server error occurred',
                ],
            ],
            $this->calls[1],
        );

        self::assertSame(
            [
                'object' => 'HeroFactory',
                'method' => 'createFromDefaults',
                'args' => [],
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

        self::assertCount(
            2,
            $this->calls[3]['args'],
        );

        self::assertSame(
            '@app/Http/Response/Error/Error500.twig',
            $this->calls[3]['args'][0],
        );

        $context = $this->calls[3]['args'][1];

        self::assertCount(2, $context);

        assert($context['meta'] instanceof Meta);

        self::assertSame(
            'Internal Server Error',
            $context['meta']->metaTitle(),
        );

        self::assertSame($this->hero, $context['hero']);

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
