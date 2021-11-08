<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail;

use App\Email\Entities\EmailRecipient;
use App\Http\Shared\Exceptions\InvalidEmailAddress;
use craft\config\GeneralConfig;
use craft\elements\GlobalSet;
use craft\errors\InvalidFieldException;
use craft\services\Globals;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 */
class GetEmailRecipientsTest extends TestCase
{
    private bool $devMode = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->devMode = false;
    }

    /**
     * @return MockObject&Globals
     */
    private function mockGlobals(): mixed
    {
        $generalSet = $this->createMock(GlobalSet::class);

        $generalSet->method('getFieldValue')->willReturnCallback(
            static function (string $fieldHandle): array {
                /** @phpstan-ignore-next-line */
                return match ($fieldHandle) {
                    'contactFormRecipients' => [
                        ['emailAddress' => 'foo@bar.baz'],
                        ['emailAddress' => 'bar@baz.foo'],
                    ],
                };
            }
        );

        $globals = $this->createMock(Globals::class);

        $globals->method('getSetByHandle')->willReturnCallback(
            static function (string $handle) use (
                $generalSet,
            ): GlobalSet {
                /** @phpstan-ignore-next-line */
                return match ($handle) {
                    'general' => $generalSet,
                };
            }
        );

        return $globals;
    }

    /**
     * @return MockObject&GeneralConfig
     */
    private function mockGeneralConfig(): mixed
    {
        $generalConfig = $this->createMock(
            GeneralConfig::class,
        );

        $generalConfig->devMode = $this->devMode;

        return $generalConfig;
    }

    /**
     * @throws InvalidEmailAddress
     * @throws InvalidFieldException
     */
    public function testGetInDevMode(): void
    {
        $this->devMode = true;

        $getEmailRecipients = new GetEmailRecipients(
            globals: $this->mockGlobals(),
            generalConfig: $this->mockGeneralConfig(),
        );

        $collection = $getEmailRecipients->get();

        $items = $collection->map(
            static fn (
                EmailRecipient $e,
            ) => $e->emailAddress()->toString(),
        );

        self::assertSame(
            ['tj@buzzingpixel.com'],
            $items,
        );
    }

    /**
     * @throws InvalidEmailAddress
     * @throws InvalidFieldException
     */
    public function testGetInNormalMode(): void
    {
        $getEmailRecipients = new GetEmailRecipients(
            globals: $this->mockGlobals(),
            generalConfig: $this->mockGeneralConfig(),
        );

        $collection = $getEmailRecipients->get();

        $items = $collection->map(
            static fn (
                EmailRecipient $e,
            ) => $e->emailAddress()->toString(),
        );

        self::assertSame(
            [
                'foo@bar.baz',
                'bar@baz.foo',
            ],
            $items,
        );
    }
}
