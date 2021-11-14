<?php

declare(strict_types=1);

namespace App\Messages;

use App\Messages\RetrieveMessages\MessageRetrieval;
use App\Messages\RetrieveMessages\MessageRetrievalParams;
use App\Messages\RetrieveMessages\MessagesResult;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress PossiblyFalseArgument
 * @psalm-suppress PropertyNotSetInConstructor
 */
class MessagesApiTest extends TestCase
{
    private MessagesApi $messagesApi;

    /** @var mixed[] */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->messagesApi = new MessagesApi(
            $this->mockMessageRetrieval(),
        );
    }

    /**
     * @return MessageRetrieval&MockObject
     */
    private function mockMessageRetrieval(): mixed
    {
        $messageRetrieval = $this->createMock(
            MessageRetrieval::class,
        );

        $messageRetrieval->method('fromParams')->willReturnCallback(
            function (MessageRetrievalParams $params): MessagesResult {
                $this->calls[] = [
                    'object' => 'MessageRetrieval',
                    'method' => 'fromParams',
                    'params' => $params,
                ];

                return new MessagesResult(
                    absoluteTotal: 42,
                    messages: [],
                );
            }
        );

        return $messageRetrieval;
    }

    public function testRetrieveMessages(): void
    {
        $params = new MessageRetrievalParams();

        $result = $this->messagesApi->retrieveMessages(params: $params);

        self::assertSame(42, $result->absoluteTotal());

        self::assertSame(
            [
                [
                    'object' => 'MessageRetrieval',
                    'method' => 'fromParams',
                    'params' => $params,
                ],
            ],
            $this->calls,
        );
    }
}
