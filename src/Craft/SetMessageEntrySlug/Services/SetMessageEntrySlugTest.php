<?php

declare(strict_types=1);

namespace App\Craft\SetMessageEntrySlug\Services;

use craft\elements\Entry;
use DateTime;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

use function assert;

/** @psalm-suppress PropertyNotSetInConstructor */
class SetMessageEntrySlugTest extends TestCase
{
    public function testSet(): void
    {
        $postDateStub = DateTime::createFromFormat(
            DateTimeInterface::ATOM,
            '1982-01-27T10:00:10+00:00'
        );

        assert($postDateStub instanceof DateTime);

        $entryStub = $this->createMock(Entry::class);

        $entryStub->title = 'Test Title';

        $entryStub->postDate = $postDateStub;

        $setMessageEntrySlug = new SetMessageEntrySlug(entry: $entryStub);

        self::assertNull($entryStub->slug);

        $setMessageEntrySlug->set();

        /** @psalm-suppress DocblockTypeContradiction */
        self::assertSame(
            '1982-01-27-test-title',
            $entryStub->slug,
        );
    }
}
