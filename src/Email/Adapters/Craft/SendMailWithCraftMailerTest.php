<?php

declare(strict_types=1);

namespace App\Email\Adapters\Craft;

use App\Email\Entities\Email;
use App\Email\Entities\EmailRecipient;
use App\Email\Entities\EmailRecipientCollection;
use craft\mail\Mailer as CraftMailer;
use craft\mail\Message as CraftMessage;
use craft\models\MailSettings as CraftMailSettings;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function assert;

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

        $craftMailer->method('send')->willReturnCallback(
            function (CraftMessage $message): bool {
                $this->calls[] = [
                    'object' => 'CraftMailer',
                    'method' => 'send',
                    'message' => $message,
                ];

                return true;
            }
        );

        return $craftMailer;
    }

    /**
     * @return MockObject&CraftMailSettings
     *
     * @phpstan-ignore-next-line
     */
    private function mockCraftMailSettings(): mixed
    {
        $settings = $this->createMock(
            CraftMailSettings::class,
        );

        $settings->fromEmail = 'test@from.com';

        $settings->fromName = 'Test From';

        return $settings;
    }

    public function testSend(): void
    {
        $email = new Email(
            subject: 'Test Subject',
            recipients: new EmailRecipientCollection(
                recipients: [
                    new EmailRecipient(
                        emailAddress: 'testto1@foo.bar',
                        name: 'Test To 1 Foo Bar',
                    ),
                    new EmailRecipient(
                        emailAddress: 'testto2@foo.bar',
                        name: 'Test To 2 Foo Bar',
                    ),
                ],
            ),
            from: new EmailRecipient(
                emailAddress: 'testfrom1@foo.bar',
                name: 'Test From Foo Bar',
            ),
            plaintext: 'Test Plain Text',
            html: 'Test Html',
        );

        $result = $this->sendMail->send(email: $email);

        self::assertTrue($result->sentSuccessfully());

        self::assertCount(1, $this->calls);

        self::assertSame(
            'CraftMailer',
            $this->calls[0]['object'],
        );

        self::assertSame(
            'send',
            $this->calls[0]['method'],
        );

        $message = $this->calls[0]['message'];

        assert($message instanceof CraftMessage);

        self::assertSame(
            'Test Subject',
            $message->getSubject(),
        );

        self::assertSame(
            [
                'testto1@foo.bar' => 'Test To 1 Foo Bar',
                'testto2@foo.bar' => 'Test To 2 Foo Bar',
            ],
            $message->getTo(),
        );

        self::assertSame(
            ['test@from.com' => 'Test From'],
            $message->getFrom(),
        );

        self::assertSame(
            ['testfrom1@foo.bar' => 'Test From Foo Bar'],
            $message->getReplyTo(),
        );
    }
}
