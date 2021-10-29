<?php

declare(strict_types=1);

namespace App\Shared\Entries;

use craft\elements\Entry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class EntryHandlerTest extends TestCase
{
    private EntryHandler $entryHandler;

    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $rootEntry;

    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $subEntry1;

    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $subEntry2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rootEntry = $this->createMock(Entry::class);

        $this->rootEntry->method('getParent')->willReturn(
            null,
        );

        $this->subEntry1 = $this->createMock(Entry::class);

        $this->subEntry1->method('getParent')->willReturn(
            $this->rootEntry,
        );

        $this->subEntry2 = $this->createMock(Entry::class);

        $this->subEntry2->method('getParent')->willReturn(
            $this->subEntry1,
        );

        $this->entryHandler = new EntryHandler();
    }

    public function testGetRootEntryFromSubEntry2(): void
    {
        self::assertSame(
            $this->rootEntry,
            $this->entryHandler->getRootEntry(
                entry: $this->subEntry2,
            ),
        );
    }

    public function testGetRootEntryFromSubEntry1(): void
    {
        self::assertSame(
            $this->rootEntry,
            $this->entryHandler->getRootEntry(
                entry: $this->subEntry1,
            ),
        );
    }

    public function testGetRootEntryFromRootEntry(): void
    {
        self::assertSame(
            $this->rootEntry,
            $this->entryHandler->getRootEntry(
                entry: $this->rootEntry,
            ),
        );
    }
}
