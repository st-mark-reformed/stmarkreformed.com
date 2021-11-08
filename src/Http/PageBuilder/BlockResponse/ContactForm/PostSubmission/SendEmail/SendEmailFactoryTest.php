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
class SendEmailFactoryTest extends TestCase
{
    private SendEmailFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new SendEmailFactory(
            sendEmail: $this->createMock(SendEmail::class),
            insufficientInputToSendEmail: $this->createMock(
                InsufficientInputToSendEmail::class,
            ),
        );
    }

    public function testMakeWhenFormInvalid(): void
    {
        $formValues = $this->createMock(FormValues::class);

        $formValues->method('isNotValid')->willReturn(true);

        self::assertInstanceOf(
            InsufficientInputToSendEmail::class,
            $this->factory->make($formValues),
        );
    }

    public function testMakeWhenFormIsValid(): void
    {
        $formValues = $this->createMock(FormValues::class);

        $formValues->method('isNotValid')->willReturn(false);

        self::assertInstanceOf(
            SendEmail::class,
            $this->factory->make($formValues),
        );
    }
}
