<?php

declare(strict_types=1);

namespace App\Profiles;

use App\Profiles\Queue\SetHasMessagesOnAllProfilesQueueJob;
use App\Profiles\SetHasMessages\SetHasMessagesOnAllProfiles;
use craft\queue\Queue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function assert;
use function debug_backtrace;
use function is_array;

class ProfilesApiTest extends TestCase
{
    private ProfilesApi $api;

    /** @var mixed[] */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->api = new ProfilesApi(
            queue: $this->mockQueue(),
            setHasMessagesOnAllProfiles: $this->mockSetHasMessagesOnAllProfiles(),
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
     * @return Queue&MockObject
     */
    private function mockQueue(): mixed
    {
        $queue = $this->createMock(Queue::class);

        $queue->method(self::anything())->willReturnCallback(
            function (): mixed {
                return $this->genericCall(
                    object: 'Queue',
                    return: true,
                );
            }
        );

        return $queue;
    }

    /**
     * @return SetHasMessagesOnAllProfiles&MockObject
     */
    private function mockSetHasMessagesOnAllProfiles(): mixed
    {
        $set = $this->createMock(
            SetHasMessagesOnAllProfiles::class,
        );

        $set->method(self::anything())->willReturnCallback(
            function (): void {
                $this->genericCall(
                    object: 'SetHasMessagesOnAllProfiles',
                );
            }
        );

        return $set;
    }

    public function testSetHasMessagesOnAllProfiles(): void
    {
        $this->api->setHasMessagesOnAllProfiles();

        self::assertSame(
            [
                [
                    'object' => 'SetHasMessagesOnAllProfiles',
                    'method' => 'set',
                    'args' => [],
                ],
            ],
            $this->calls,
        );
    }

    public function testQueueSetMessageSeriesLatestEntry(): void
    {
        $this->api->queueSetHasMessagesOnAllProfiles();

        self::assertCount(1, $this->calls);

        self::assertSame(
            'Queue',
            $this->calls[0]['object'],
        );

        self::assertSame(
            'push',
            $this->calls[0]['method'],
        );

        $args = $this->calls[0]['args'];

        assert(is_array($args));

        self::assertCount(1, $args);

        self::assertInstanceOf(
            SetHasMessagesOnAllProfilesQueueJob::class,
            $args[0],
        );
    }
}
