<?php

declare(strict_types=1);

namespace App\Messages\Events;

use App\Messages\MessagesApi;
use craft\base\Element;
use craft\elements\Entry;
use craft\models\EntryType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use yii\base\Event;
use yii\base\InvalidConfigException;

use function debug_backtrace;

/**
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress PossiblyFalseArgument
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ModifyElementQueueSetMessageSeriesLatestEntryTest extends TestCase
{
    private ModifyElementQueueSetMessageSeriesLatestEntry $responder;

    /** @var mixed[] */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->responder = new ModifyElementQueueSetMessageSeriesLatestEntry(
            messagesApi: $this->mockMessagesApi(),
        );
    }

    /**
     * @psalm-suppress PossiblyUndefinedArrayOffset
     */
    private function genericCall(
        string $object,
        mixed $return = null
    ): mixed {
        $trace = debug_backtrace()[5];

        $this->calls[] = [
            'object' => $object,
            'method' => $trace['function'],
            'args' => $trace['args'],
        ];

        return $return;
    }

    /**
     * @return MessagesApi&MockObject
     */
    private function mockMessagesApi(): mixed
    {
        $api = $this->createMock(MessagesApi::class);

        $api->method(self::anything())->willReturnCallback(
            function (): void {
                $this->genericCall(object: 'MessagesApi');
            }
        );

        return $api;
    }

    /**
     * @throws InvalidConfigException
     */
    public function testWhenElementIsNotEntry(): void
    {
        $element = $this->createMock(Element::class);

        $event = $this->createMock(Event::class);

        $event->sender = $element;

        $this->responder->respond(event: $event);

        self::assertSame([], $this->calls);
    }

    /**
     * @throws InvalidConfigException
     */
    public function testWhenHandleIsNotMessages(): void
    {
        $type = $this->createMock(EntryType::class);

        $type->handle = 'fooBar';

        $element = $this->createMock(Entry::class);

        $element->method('getType')->willReturn($type);

        $event = $this->createMock(Event::class);

        $event->sender = $element;

        $this->responder->respond(event: $event);

        self::assertSame([], $this->calls);
    }

    /**
     * @throws InvalidConfigException
     */
    public function testWhenHandleIsMessages(): void
    {
        $type = $this->createMock(EntryType::class);

        $type->handle = 'messages';

        $element = $this->createMock(Entry::class);

        $element->method('getType')->willReturn($type);

        $event = $this->createMock(Event::class);

        $event->sender = $element;

        $this->responder->respond(event: $event);

        self::assertSame(
            [
                [
                    'object' => 'MessagesApi',
                    'method' => 'queueSetMessageSeriesLatestEntry',
                    'args' => [],
                ],
            ],
            $this->calls
        );
    }
}
