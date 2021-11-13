<?php

declare(strict_types=1);

namespace App\Craft\Behaviors;

use craft\elements\Entry;
use craft\fields\data\SingleOptionFieldData;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 * @psalm-suppress MixedArgument
 */
class ProfileEntriesBehaviorTest extends TestCase
{
    private ProfileEntriesBehavior $behavior;

    /** @var mixed[] */
    private array $calls = [];

    private bool $getFieldValueThrowsExceptionForHonorific = false;

    private bool $getFieldValueThrowsExceptionForPosition = false;

    private string $honorificValue = '';

    private string $positionLabel = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->getFieldValueThrowsExceptionForHonorific = false;

        $this->getFieldValueThrowsExceptionForPosition = false;

        $this->honorificValue = '';

        $this->positionLabel = '';

        $this->behavior = new ProfileEntriesBehavior();

        $this->behavior->owner = $this->mockEntry();
    }

    /**
     * @return Entry&MockObject
     *
     * @phpstan-ignore-next-line
     * @noinspection PhpMixedReturnTypeCanBeReducedInspection
     */
    private function mockEntry(): mixed
    {
        $entry = $this->createMock(Entry::class);

        $entry->title = 'foo test title';

        $entry->method('getFieldValue')->willReturnCallback(
            function (string $fieldHandle): mixed {
                $this->calls[] = [
                    'object' => 'Entry',
                    'method' => 'getFieldValue',
                    'fieldHandle' => $fieldHandle,
                ];

                if (
                    $fieldHandle === 'titleOrHonorific' &&
                    $this->getFieldValueThrowsExceptionForHonorific
                ) {
                    throw new Exception();
                }

                if (
                    $fieldHandle === 'leadershipPosition' &&
                    $this->getFieldValueThrowsExceptionForPosition
                ) {
                    throw new Exception();
                }

                $option = new SingleOptionFieldData(
                    $this->positionLabel,
                    'fooValue',
                    true,
                    true,
                );

                /** @phpstan-ignore-next-line */
                return match ($fieldHandle) {
                    'titleOrHonorific' => $this->honorificValue,
                    'leadershipPosition' => $option,
                };
            }
        );

        return $entry;
    }

    public function testFullNameHonorificWhenThrows(): void
    {
        $this->getFieldValueThrowsExceptionForHonorific = true;

        self::assertSame(
            'foo test title',
            $this->behavior->fullNameHonorific(),
        );

        self::assertSame(
            [
                [
                    'object' => 'Entry',
                    'method' => 'getFieldValue',
                    'fieldHandle' => 'titleOrHonorific',
                ],
            ],
            $this->calls,
        );
    }

    public function testFullNameHonorificWhenNoValue(): void
    {
        self::assertSame(
            'foo test title',
            $this->behavior->fullNameHonorific(),
        );

        self::assertSame(
            [
                [
                    'object' => 'Entry',
                    'method' => 'getFieldValue',
                    'fieldHandle' => 'titleOrHonorific',
                ],
            ],
            $this->calls,
        );
    }

    public function testFullNameHonorificWhenValue(): void
    {
        $this->honorificValue = 'bar honorific';

        self::assertSame(
            'bar honorific foo test title',
            $this->behavior->fullNameHonorific(),
        );

        self::assertSame(
            [
                [
                    'object' => 'Entry',
                    'method' => 'getFieldValue',
                    'fieldHandle' => 'titleOrHonorific',
                ],
            ],
            $this->calls,
        );
    }

    public function testFullNameAppendWhenThrows(): void
    {
        $this->getFieldValueThrowsExceptionForPosition = true;

        self::assertSame(
            'foo test title',
            $this->behavior->fullNameHonorificAppendedPosition(),
        );

        self::assertSame(
            [
                [
                    'object' => 'Entry',
                    'method' => 'getFieldValue',
                    'fieldHandle' => 'titleOrHonorific',
                ],
                [
                    'object' => 'Entry',
                    'method' => 'getFieldValue',
                    'fieldHandle' => 'leadershipPosition',
                ],
            ],
            $this->calls,
        );
    }

    public function testFullNameAppendWhenNoValue(): void
    {
        self::assertSame(
            'foo test title',
            $this->behavior->fullNameHonorificAppendedPosition(),
        );

        self::assertSame(
            [
                [
                    'object' => 'Entry',
                    'method' => 'getFieldValue',
                    'fieldHandle' => 'titleOrHonorific',
                ],
                [
                    'object' => 'Entry',
                    'method' => 'getFieldValue',
                    'fieldHandle' => 'leadershipPosition',
                ],
            ],
            $this->calls,
        );
    }

    public function testFullNameAppendWhenValue(): void
    {
        $this->positionLabel = 'fooLabel';

        self::assertSame(
            'foo test title (fooLabel)',
            $this->behavior->fullNameHonorificAppendedPosition(),
        );

        self::assertSame(
            [
                [
                    'object' => 'Entry',
                    'method' => 'getFieldValue',
                    'fieldHandle' => 'titleOrHonorific',
                ],
                [
                    'object' => 'Entry',
                    'method' => 'getFieldValue',
                    'fieldHandle' => 'leadershipPosition',
                ],
            ],
            $this->calls,
        );
    }
}
