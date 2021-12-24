<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Sidebar;

use App\Http\Components\Link\Link;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\Testing\TestCase;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\fields\data\SingleOptionFieldData;
use PHPUnit\Framework\MockObject\MockObject;

use function array_map;
use function assert;

/**
 * @codeCoverageIgnore
 */
class RetrieveLeadersWithMessagesTest extends TestCase
{
    private RetrieveLeadersWithMessages $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new RetrieveLeadersWithMessages(
            queryFactory: $this->mockEntryQueryFactory(),
        );
    }

    /**
     * @return EntryQueryFactory&MockObject
     */
    private function mockEntryQueryFactory(): mixed
    {
        $mock = $this->createMock(EntryQueryFactory::class);

        $mock->method('make')->willReturn(
            $this->mockEntryQuery(),
        );

        return $mock;
    }

    /**
     * @return EntryQuery&MockObject
     *
     * @phpstan-ignore-next-line
     */
    private function mockEntryQuery(): mixed
    {
        $speaker1 = $this->createMock(Entry::class);

        $speaker1->slug = 'slug1';

        $speaker1->method('getFieldValue')
            ->willReturnCallback([
                $this,
                'getFieldValue',
            ]);

        $speaker1->method('__call')
            ->willReturnCallback([
                $this,
                'fullNameHonorificAppendedPosition',
            ]);

        $speaker2 = $this->createMock(Entry::class);

        $speaker2->slug = 'slug2';

        $speaker2->method('getFieldValue')
            ->willReturnCallback([
                $this,
                'getFieldValue',
            ]);

        $speaker2->method('__call')
            ->willReturnCallback([
                $this,
                'fullNameHonorificAppendedPosition',
            ]);

        $mock = $this->createMock(EntryQuery::class);

        $callback = function () use ($mock): EntryQuery {
            return $this->genericCall(
                object: 'EntryQuery',
                return: $mock,
            );
        };

        $mock->method('section')->willReturnCallback(
            $callback
        );

        $mock->method('orderBy')->willReturnCallback(
            $callback
        );

        $mock->method('__call')->willReturnCallback(
            $callback,
        );

        $mock->method('all')->willReturn([
            $speaker1,
            $speaker2,
        ]);

        return $mock;
    }

    private int $positionCallNumber = 0;

    public function getFieldValue(string $fieldHandle): SingleOptionFieldData
    {
        $this->positionCallNumber += 1;

        assert($fieldHandle === 'leadershipPosition');

        $mock = $this->createMock(
            SingleOptionFieldData::class,
        );

        $mock->value = $this->positionCallNumber === 1 ? 'test' : null;

        return $mock;
    }

    private int $honorificCallNumber = 0;

    public function fullNameHonorificAppendedPosition(string $method): string
    {
        $this->honorificCallNumber += 1;

        assert($method === 'fullNameHonorificAppendedPosition');

        return $this->honorificCallNumber === 1 ?
            'Test Honorific Name 1' :
            'Test Honorific Name 2';
    }

    public function testRetrieve(): void
    {
        self::assertSame(
            [
                [
                    'isEmpty' => false,
                    'content' => 'Test Honorific Name 1',
                    'href' => '/media/messages?by%5B0%5D=slug1',
                    'newWindow' => false,
                ],
                [
                    'isEmpty' => false,
                    'content' => 'Test Honorific Name 2',
                    'href' => '/media/messages?by%5B0%5D=slug2',
                    'newWindow' => false,
                ],
            ],
            array_map(
                static fn (Link $link) => [
                    'isEmpty' => $link->isEmpty(),
                    'content' => $link->content(),
                    'href' => $link->href(),
                    'newWindow' => $link->newWindow(),
                ],
                $this->service->retrieve(),
            ),
        );

        self::assertSame(
            [
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'args' => ['profiles'],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'orderBy',
                    'args' => ['lastName asc'],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => '__call',
                    'args' => [
                        'leadershipPosition',
                        [
                            [
                                'pastor',
                                'elder',
                                'rulingElder',
                                'deacon',
                            ],
                        ],
                    ],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => '__call',
                    'args' => [
                        'hasMessages',
                        [true],
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
