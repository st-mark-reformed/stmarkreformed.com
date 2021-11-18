<?php

declare(strict_types=1);

namespace App\Messages\SetMessageSeriesLatestEntryDate\SetFromEntry;

use craft\elements\Entry;
use PHPUnit\Framework\TestCase;

class SetFromMessageFactoryTest extends TestCase
{
    private SetFromMessageFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new SetFromMessageFactory(
            setToNull: $this->createMock(SetToNull::class),
            setFromMessage: $this->createMock(
                SetFromMessage::class,
            ),
        );
    }

    public function testWhenNoMessage(): void
    {
        self::assertInstanceOf(
            SetToNull::class,
            $this->factory->make(null),
        );
    }

    public function testWhenMessage(): void
    {
        self::assertInstanceOf(
            SetFromMessage::class,
            $this->factory->make($this->createMock(
                Entry::class,
            )),
        );
    }
}
