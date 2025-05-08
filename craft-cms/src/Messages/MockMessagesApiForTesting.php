<?php

declare(strict_types=1);

namespace App\Messages;

use App\Messages\RetrieveMessages\MessagesResult;
use App\Shared\Testing\TestCase;
use craft\elements\Entry;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;

trait MockMessagesApiForTesting
{
    /**
     * @return MessagesApi&MockObject
     */
    public function mockMessagesApi(): mixed
    {
        assert($this instanceof TestCase);

        $message1 = $this->createMock(Entry::class);

        $message1->slug = 'message-1';

        $message2 = $this->createMock(Entry::class);

        $message2->slug = 'message-2';

        $mock = $this->createMock(MessagesApi::class);

        $mock->method('retrieveMessages')->willReturnCallback(
            function () use (
                $message1,
                $message2,
            ): MessagesResult {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    'MessagesApi',
                    new MessagesResult(
                        absoluteTotal: 123,
                        messages: [
                            $message1,
                            $message2,
                        ],
                    ),
                );
            }
        );

        return $mock;
    }
}
