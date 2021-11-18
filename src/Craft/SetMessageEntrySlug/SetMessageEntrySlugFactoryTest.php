<?php

declare(strict_types=1);

namespace App\Craft\SetMessageEntrySlug;

use App\Craft\SetMessageEntrySlug\Services\DoNotSetSlug;
use App\Craft\SetMessageEntrySlug\Services\SetMessageEntrySlug;
use craft\elements\Category;
use craft\elements\Entry;
use craft\events\ModelEvent;
use craft\models\Section;
use DateTime;
use DateTimeInterface;
use Exception;
use PHPUnit\Framework\TestCase;

use function assert;

class SetMessageEntrySlugFactoryTest extends TestCase
{
    public function testMakeWhenExceptionThrown(): void
    {
        $entryStub = $this->createMock(Entry::class);

        $entryStub->method('getSection')->willThrowException(
            new Exception(),
        );

        $eventModelStub = $this->createMock(
            ModelEvent::class
        );

        $eventModelStub->sender = $entryStub;

        $factory = new SetMessageEntrySlugFactory();

        self::assertInstanceOf(
            DoNotSetSlug::class,
            $factory->make($eventModelStub),
        );
    }

    public function testMakeWhenElementIsCategory(): void
    {
        $categoryStub = $this->createMock(Category::class);

        $eventModelStub = $this->createMock(
            ModelEvent::class
        );

        $eventModelStub->sender = $categoryStub;

        $factory = new SetMessageEntrySlugFactory();

        self::assertInstanceOf(
            DoNotSetSlug::class,
            $factory->make($eventModelStub),
        );
    }

    public function testMakeWhenSectionIsNotOnTheList(): void
    {
        $sectionStub = $this->createMock(Section::class);

        $sectionStub->handle = 'testHandle';

        $entryStub = $this->createMock(Entry::class);

        $entryStub->method('getSection')->willReturn(
            $sectionStub,
        );

        $eventModelStub = $this->createMock(
            ModelEvent::class
        );

        $eventModelStub->sender = $entryStub;

        $factory = new SetMessageEntrySlugFactory();

        self::assertInstanceOf(
            DoNotSetSlug::class,
            $factory->make($eventModelStub),
        );
    }

    public function testMakeWhenPostDateIsNull(): void
    {
        $sectionStub = $this->createMock(Section::class);

        $sectionStub->handle = 'messages';

        $entryStub = $this->createMock(Entry::class);

        $entryStub->method('getSection')->willReturn(
            $sectionStub,
        );

        $entryStub->postDate = null;

        $eventModelStub = $this->createMock(
            ModelEvent::class
        );

        $eventModelStub->sender = $entryStub;

        $factory = new SetMessageEntrySlugFactory();

        self::assertInstanceOf(
            DoNotSetSlug::class,
            $factory->make($eventModelStub),
        );
    }

    public function testMakeWhenPostDateIsNotNull(): void
    {
        $postDateStub = DateTime::createFromFormat(
            DateTimeInterface::ATOM,
            '1982-01-27T10:00:10+00:00'
        );

        assert($postDateStub instanceof DateTime);

        $sectionStub = $this->createMock(Section::class);

        $sectionStub->handle = 'messages';

        $entryStub = $this->createMock(Entry::class);

        $entryStub->method('getSection')->willReturn(
            $sectionStub,
        );

        $entryStub->postDate = $postDateStub;

        $eventModelStub = $this->createMock(
            ModelEvent::class
        );

        $eventModelStub->sender = $entryStub;

        $factory = new SetMessageEntrySlugFactory();

        self::assertInstanceOf(
            SetMessageEntrySlug::class,
            $factory->make($eventModelStub),
        );
    }
}
