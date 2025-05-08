<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Response;

use App\Messages\RetrieveMessages\MessagesResult;
use PHPUnit\Framework\TestCase;

class ResponderFactoryTest extends TestCase
{
    private ResponderFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ResponderFactory(
            respondWithResults: $this->createMock(
                RespondWithResults::class,
            ),
            respondWithNoResults: $this->createMock(
                RespondWithNoResults::class,
            ),
        );
    }

    public function testWhenCountIsOne(): void
    {
        $result = $this->createMock(MessagesResult::class);

        $result->method('count')->willReturn(1);

        self::assertInstanceOf(
            RespondWithResults::class,
            $this->factory->make(result: $result),
        );
    }

    public function testWhenCountIsTwo(): void
    {
        $result = $this->createMock(MessagesResult::class);

        $result->method('count')->willReturn(2);

        self::assertInstanceOf(
            RespondWithResults::class,
            $this->factory->make(result: $result),
        );
    }

    public function testWhenCountIsZero(): void
    {
        $result = $this->createMock(MessagesResult::class);

        $result->method('count')->willReturn(0);

        self::assertInstanceOf(
            RespondWithNoResults::class,
            $this->factory->make(result: $result),
        );
    }
}
