<?php

declare(strict_types=1);

namespace App\Profiles\Events;

use App\Profiles\ProfilesApi;
use App\Shared\Testing\TestCase;
use craft\base\Element;
use craft\elements\Entry;
use craft\models\EntryType;
use PHPUnit\Framework\MockObject\MockObject;
use yii\base\Event;
use yii\base\InvalidConfigException;

class ModifyElementQueueSetHasMessagesOnAllProfilesTest extends TestCase
{
    private ModifyElementQueueSetHasMessagesOnAllProfiles $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = new ModifyElementQueueSetHasMessagesOnAllProfiles(
            profilesApi: $this->mockProfilesApi(),
        );
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
