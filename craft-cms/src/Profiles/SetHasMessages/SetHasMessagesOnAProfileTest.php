<?php

declare(strict_types=1);

namespace App\Profiles\SetHasMessages;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\Testing\TestCase;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\errors\ElementNotFoundException;
use craft\errors\InvalidFieldException;
use craft\services\Elements as ElementsService;
use PHPUnit\Framework\MockObject\MockObject;
use Throwable;
use yii\base\Exception;

class SetHasMessagesOnAProfileTest extends TestCase
{
    private SetHasMessagesOnAProfile $service;

    private bool $genericBooleanReturn = false;

    private int $queryCountReturn = 0;

    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $profile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->genericBooleanReturn = false;

        $this->queryCountReturn = 0;

        $this->service = new SetHasMessagesOnAProfile(
            genericHandler: $this->mockGenericHandler(),
            queryFactory: $this->mockQueryFactory(),
            elementsService: $this->mockElementsService(),
        );

        $this->profile = $this->mockProfile();
    }

    /**
     * @return GenericHandler&MockObject
     */
    private function mockGenericHandler(): mixed
    {
        $genericHandler = $this->createMock(
            GenericHandler::class,
        );

        $genericHandler->method(self::anything())
            ->willReturnCallback(function (): bool {
                return $this->genericCall(
                    object: 'GenericHandler',
                    return: $this->genericBooleanReturn,
                );
            });

        return $genericHandler;
    }

    /**
     * @return EntryQueryFactory&MockObject
     */
    private function mockQueryFactory(): mixed
    {
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

        $query->method('relatedTo')->willReturnCallback(
            function () use ($query): EntryQuery {
                return $this->genericCall(
                    object: 'EntryQuery',
                    return: $query,
                );
            }
        );

        $query->method('count')->willReturnCallback(
            function (): int {
                return $this->genericCall(
                    object: 'EntryQuery',
                    return: $this->queryCountReturn,
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
     * @return ElementsService&MockObject
     */
    private function mockElementsService(): mixed
    {
        $service = $this->createMock(ElementsService::class);

        $service->method(self::anything())->willReturnCallback(
            function (): bool {
                return $this->genericCall(
                    object: 'ElementsService',
                    return: true,
                );
            }
        );

        return $service;
    }

    /**
     * @return Entry&MockObject
     *
     * @phpstan-ignore-next-line
     */
    private function mockProfile(): mixed
    {
        $profile = $this->createMock(Entry::class);

        $profile->method(self::anything())->willReturnCallback(
            function (): void {
                $this->genericCall(object: 'Entry');
            }
        );

        return $profile;
    }

    /**
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws InvalidFieldException
     * @throws Exception
     */
    public function testSetWhenBothAreFalse(): void
    {
        $this->service->set(profile: $this->profile);

        self::assertSame(
            [
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'args' => ['messages'],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'relatedTo',
                    'args' => [
                        [
                            'targetElement' => $this->profile,
                            'field' => 'profile',
                        ],
                    ],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'count',
                    'args' => [],
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getBoolean',
                    'args' => [
                        $this->profile,
                        'hasMessages',
                    ],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws InvalidFieldException
     * @throws Exception
     */
    public function testSetWhenQueryIsTrueAndValueIsFalse(): void
    {
        $this->queryCountReturn = 2;

        $this->service->set(profile: $this->profile);

        self::assertSame(
            [
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'args' => ['messages'],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'relatedTo',
                    'args' => [
                        [
                            'targetElement' => $this->profile,
                            'field' => 'profile',
                        ],
                    ],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'count',
                    'args' => [],
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getBoolean',
                    'args' => [
                        $this->profile,
                        'hasMessages',
                    ],
                ],
                [
                    'object' => 'Entry',
                    'method' => 'setFieldValue',
                    'args' => [
                        'hasMessages',
                        true,
                    ],
                ],
                [
                    'object' => 'ElementsService',
                    'method' => 'saveElement',
                    'args' => [
                        $this->profile,
                    ],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws InvalidFieldException
     * @throws Exception
     */
    public function testSetWhenBothAreTrue(): void
    {
        $this->queryCountReturn = 2;

        $this->genericBooleanReturn = true;

        $this->service->set(profile: $this->profile);

        self::assertSame(
            [
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'args' => ['messages'],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'relatedTo',
                    'args' => [
                        [
                            'targetElement' => $this->profile,
                            'field' => 'profile',
                        ],
                    ],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'count',
                    'args' => [],
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getBoolean',
                    'args' => [
                        $this->profile,
                        'hasMessages',
                    ],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws InvalidFieldException
     * @throws Exception
     */
    public function testSetWhenQueryIsFalseAndValueIsTrue(): void
    {
        $this->genericBooleanReturn = true;

        $this->service->set(profile: $this->profile);

        self::assertSame(
            [
                [
                    'object' => 'EntryQuery',
                    'method' => 'section',
                    'args' => ['messages'],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'relatedTo',
                    'args' => [
                        [
                            'targetElement' => $this->profile,
                            'field' => 'profile',
                        ],
                    ],
                ],
                [
                    'object' => 'EntryQuery',
                    'method' => 'count',
                    'args' => [],
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getBoolean',
                    'args' => [
                        $this->profile,
                        'hasMessages',
                    ],
                ],
                [
                    'object' => 'Entry',
                    'method' => 'setFieldValue',
                    'args' => [
                        'hasMessages',
                        false,
                    ],
                ],
                [
                    'object' => 'ElementsService',
                    'method' => 'saveElement',
                    'args' => [
                        $this->profile,
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
