<?php

declare(strict_types=1);

namespace App\Messages\Events;

use App\Messages\MessagesApi;
use App\Shared\Testing\TestCase;
use craft\base\Element;
use craft\elements\Entry;
use craft\models\EntryType;
use PHPUnit\Framework\MockObject\MockObject;
use yii\base\Event;
use yii\base\InvalidConfigException;

class ModifyElementQueueSetMessageSeriesLatestEntryTest extends TestCase
{
    private ModifyElementQueueSetMessageSeriesLatestEntry $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = new ModifyElementQueueSetMessageSeriesLatestEntry(
            messagesApi: $this->mockMessagesApi(),
        );
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
