<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\FormValues;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 */
class InsufficientInputToSendEmailTest extends TestCase
{
    public function testSend(): void
    {
        $formValues = $this->createMock(FormValues::class);

        $sendEmailResult = (new InsufficientInputToSendEmail())->send(
            formValues: $formValues,
        );

        self::assertFalse($sendEmailResult->sentSuccessfully());

        self::assertSame(
            $formValues,
            $sendEmailResult->formValues(),
        );
    }
}
