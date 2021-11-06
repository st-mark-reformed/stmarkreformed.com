<?php

declare(strict_types=1);

namespace App\Email\Adapters\Craft;

use craft\mail\Mailer as CraftMailer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class SendMailWithCraftMailerTest extends TestCase
{
    private SendMailWithCraftMailer $sendMail;

    /** @var mixed[] */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->sendMail = new SendMailWithCraftMailer(
            craftMailer: $this->mockCraftMailer(),
            craftMailSettings: $this->mockCraftMailSettings(),
        );
    }

    /**
     * @return MockObject&CraftMailer
     */
    private function mockCraftMailer(): mixed
    {
        $craftMailer = $this->createMock(CraftMailer::class);

        $craftMailer->method('');

        return $craftMailer;
    }
}
