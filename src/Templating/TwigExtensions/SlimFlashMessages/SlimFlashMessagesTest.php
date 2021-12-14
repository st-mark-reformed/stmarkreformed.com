<?php

declare(strict_types=1);

namespace App\Templating\TwigExtensions\SlimFlashMessages;

use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Slim\Flash\Messages;

use function assert;
use function is_array;

class SlimFlashMessagesTest extends TestCase
{
    private SlimFlashMessages $slimFlashMessages;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slimFlashMessages = new SlimFlashMessages(
            flash: $this->mockFlash(),
        );
    }

    /**
     * @return Messages&MockObject
     */
    private function mockFlash(): Messages|MockObject
    {
        $flash = $this->createMock(Messages::class);

        $flash->method('getMessage')->willReturnCallback(
            function (): string {
                return $this->genericCall(
                    object: 'Messages',
                    return: 'getMessageReturn',
                );
            }
        );

        $flash->method('getMessages')->willReturnCallback(
            function (): string {
                return $this->genericCall(
                    object: 'Messages',
                    return: 'getMessagesReturn',
                );
            }
        );

        return $flash;
    }

    public function testGetFunctions(): void
    {
        $functions = $this->slimFlashMessages->getFunctions();

        self::assertCount(1, $functions);

        self::assertSame('flash', $functions[0]->getName());

        $callable = $functions[0]->getCallable();

        assert(is_array($callable));

        self::assertCount(2, $callable);

        self::assertSame(
            $this->slimFlashMessages,
            $callable[0],
        );

        self::assertSame(
            'getMessages',
            $callable[1],
        );

        self::assertCount(0, $this->calls);
    }

    public function testGetMessageWhenKeyIsProvided(): void
    {
        self::assertSame(
            'getMessageReturn',
            $this->slimFlashMessages->getMessages('testKey'),
        );

        self::assertSame(
            [
                [
                    'object' => 'Messages',
                    'method' => 'getMessage',
                    'args' => ['testKey'],
                ],
            ],
            $this->calls,
        );
    }

    public function testGetMessageWhenNoKeyIsProvided(): void
    {
        self::assertSame(
            'getMessagesReturn',
            $this->slimFlashMessages->getMessages(),
        );

        self::assertSame(
            [
                [
                    'object' => 'Messages',
                    'method' => 'getMessages',
                    'args' => [],
                ],
            ],
            $this->calls,
        );
    }
}
