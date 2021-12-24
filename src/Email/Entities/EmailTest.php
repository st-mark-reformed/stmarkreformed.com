<?php

declare(strict_types=1);

namespace App\Email\Entities;

use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testValidEmail(): void
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

        self::assertTrue($email->isValid());

        self::assertFalse($email->isNotValid());

        self::assertSame([], $email->errorMessages());
    }

    public function testInvalidEmail(): void
    {
        $email = new Email(
            subject: '',
            recipients: new EmailRecipientCollection(
                recipients: [],
            ),
            from: null,
            plaintext: '',
            html: '',
        );

        self::assertFalse($email->isValid());

        self::assertTrue($email->isNotValid());

        self::assertSame(
            [
                'subject' => 'Must not be empty',
                'recipients' => 'At least one recipient is required',
                'content' => '$html or $plaintext is required',
            ],
            $email->errorMessages()
        );
    }
}
