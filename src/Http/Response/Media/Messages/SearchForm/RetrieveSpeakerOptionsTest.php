<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\SearchForm;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\Testing\TestCase;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use craft\fields\data\SingleOptionFieldData;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;

class RetrieveSpeakerOptionsTest extends TestCase
{
    private RetrieveSpeakerOptions $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new RetrieveSpeakerOptions(
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

    /**
     * @throws InvalidFieldException
     */
    public function testRetrieve(): void
    {
        $optionGroupCollection = $this->service->retrieve([
            'fooBar',
            'slug1',
        ]);

        self::assertSame(
            [
                [
                    'title' => 'St. Mark Leadership',
                    'options' => [
                        [
                            'name' => 'Test Honorific Name 1',
                            'slug' => 'slug1',
                            'isActive' => true,
                        ],
                    ],
                ],
                [
                    'title' => 'Other Speakers',
                    'options' => [
                        [
                            'name' => 'Test Honorific Name 2',
                            'slug' => 'slug2',
                            'isActive' => false,
                        ],
                    ],
                ],
            ],
            $optionGroupCollection->map(
                static fn (OptionGroup $optionGroup) => [
                    'title' => $optionGroup->groupTitle(),
                    'options' => $optionGroup->map(
                        static fn (SelectOption $option) => [
                            'name' => $option->name(),
                            'slug' => $option->slug(),
                            'isActive' => $option->isActive(),
                        ],
                    ),
                ],
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
            ],
            $this->calls,
        );
    }
}
