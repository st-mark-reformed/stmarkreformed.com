<?php

declare(strict_types=1);

namespace App\Profiles\SetHasMessages;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function assert;
use function debug_backtrace;
use function is_array;

/**
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress PossiblyFalseArgument
 * @psalm-suppress PropertyNotSetInConstructor
 */
class SetHasMessagesOnAllProfilesTest extends TestCase
{
    private SetHasMessagesOnAllProfiles $service;

    /** @var mixed[] */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->service = new SetHasMessagesOnAllProfiles(
            queryFactory: $this->mockQueryFactory(),
            setHasMessagesOnAProfile: $this->mockSetHasMessagesOnAProfile(),
        );
    }

    /**
     * @param R $return
     *
     * @return R
     *
     * @template R
     * @psalm-suppress PossiblyUndefinedArrayOffset
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
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
     * @return EntryQueryFactory&MockObject
     */
    private function mockQueryFactory(): mixed
    {
        $entry1       = $this->createMock(Entry::class);
        $entry1->slug = 'entry1Slug';

        $entry2       = $this->createMock(Entry::class);
        $entry2->slug = 'entry2Slug';

        $query = $this->createMock(
            EntryQuery::class,
        );

        $query->method('section')->willReturnCallback(
            function () use ($query): EntryQuery {
                return $this->genericCall(
                    object: 'EntryQuery',
                    return: $query,
                );
            }
        );

        $query->method('all')->willReturnCallback(
            function () use ($entry1, $entry2): array {
                return $this->genericCall(
                    object: 'EntryQuery',
                    return: [$entry1, $entry2],
                );
            }
        );

        $factory = $this->createMock(
            EntryQueryFactory::class,
        );

        $factory->method('make')->willReturn($query);

        return $factory;
    }

    /**
     * @return SetHasMessagesOnAProfile&MockObject
     */
    private function mockSetHasMessagesOnAProfile(): mixed
    {
        $set = $this->createMock(
            SetHasMessagesOnAProfile::class,
        );

        $set->method(self::anything())->willReturnCallback(
            function (): void {
                $this->genericCall(object: 'SetHasMessagesOnAProfile');
            }
        );

        return $set;
    }

    public function testSet(): void
    {
        $this->service->set();

        self::assertCount(4, $this->calls);

        self::assertSame(
            [
                'object' => 'EntryQuery',
                'method' => 'section',
                'args' => ['profiles'],
            ],
            $this->calls[0],
        );

        self::assertSame(
            [
                'object' => 'EntryQuery',
                'method' => 'all',
                'args' => [],
            ],
            $this->calls[1],
        );

        self::assertSame(
            'SetHasMessagesOnAProfile',
            $this->calls[2]['object'],
        );

        self::assertSame(
            'set',
            $this->calls[2]['method'],
        );

        $call3Args = $this->calls[2]['args'];

        assert(is_array($call3Args));

        self::assertCount(1, $call3Args);

        $call3Entry = $call3Args[0];

        assert($call3Entry instanceof Entry);

        self::assertSame(
            'entry1Slug',
            $call3Entry->slug,
        );

        self::assertSame(
            'SetHasMessagesOnAProfile',
            $this->calls[3]['object'],
        );

        self::assertSame(
            'set',
            $this->calls[3]['method'],
        );

        $call4Args = $this->calls[3]['args'];

        assert(is_array($call4Args));

        self::assertCount(1, $call4Args);

        $call4Entry = $call4Args[0];

        assert($call4Entry instanceof Entry);

        self::assertSame(
            'entry2Slug',
            $call4Entry->slug,
        );
    }
}
