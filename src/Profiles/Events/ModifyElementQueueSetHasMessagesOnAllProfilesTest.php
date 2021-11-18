<?php

declare(strict_types=1);

namespace App\Profiles\Events;

use App\Profiles\ProfilesApi;
use craft\base\Element;
use craft\elements\Entry;
use craft\models\EntryType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use yii\base\Event;
use yii\base\InvalidConfigException;

use function debug_backtrace;

class ModifyElementQueueSetHasMessagesOnAllProfilesTest extends TestCase
{
    private ModifyElementQueueSetHasMessagesOnAllProfiles $responder;

    /** @var mixed[] */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->responder = new ModifyElementQueueSetHasMessagesOnAllProfiles(
            profilesApi: $this->mockProfilesApi(),
        );
    }

    /**
     * @param R $return
     *
     * @return R
     *
     * @template R
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
     * @return ProfilesApi&MockObject
     */
    private function mockProfilesApi(): mixed
    {
        $api = $this->createMock(ProfilesApi::class);

        $api->method(self::anything())->willReturnCallback(
            function (): void {
                $this->genericCall(object: 'ProfilesApi');
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
                    'object' => 'ProfilesApi',
                    'method' => 'queueSetHasMessagesOnAllProfiles',
                    'args' => [],
                ],
            ],
            $this->calls
        );
    }
}
