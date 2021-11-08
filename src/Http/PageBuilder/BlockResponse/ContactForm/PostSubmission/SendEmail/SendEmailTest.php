<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail;

use App\Email\EmailApi;
use App\Email\Entities\Email;
use App\Email\Entities\EmailRecipient;
use App\Email\Entities\EmailRecipientCollection;
use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\FormValues;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment as TwigEnvironment;

use function assert;
use function is_array;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 * @psalm-suppress PossiblyNullReference
 */
class SendEmailTest extends TestCase
{
    private SendEmail $sendEmail;

    /** @var mixed[] */
    private array $calls = [];

    private EmailRecipientCollection $recipientCollection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->recipientCollection = new EmailRecipientCollection(
            recipients: [],
        );

        $this->sendEmail = new SendEmail(
            emailApi: $this->mockEmailApi(),
            twig: $this->mockTwig(),
            getEmailRecipients: $this->mockGetEmailRecipients(),
        );
    }

    /**
     * @return MockObject&EmailApi
     */
    private function mockEmailApi(): mixed
    {
        $emailApi = $this->createMock(
            EmailApi::class,
        );

        $emailApi->method('enqueue')->willReturnCallback(
            function (Email $email): void {
                $this->calls[] = [
                    'object' => 'EmailApi',
                    'method' => 'enqueue',
                    'email' => $email,
                ];
            }
        );

        return $emailApi;
    }

    /**
     * @return MockObject&TwigEnvironment
     */
    private function mockTwig(): mixed
    {
        $twig = $this->createMock(TwigEnvironment::class);

        $twig->method('render')->willReturnCallback(
            function (string $name, array $context): string {
                $this->calls[] = [
                    'object' => 'TwigEnvironment',
                    'method' => 'render',
                    'name' => $name,
                    'context' => $context,
                ];

                return 'twigRenderResponse';
            }
        );

        return $twig;
    }

    /**
     * @return MockObject&GetEmailRecipients
     */
    private function mockGetEmailRecipients(): mixed
    {
        $get = $this->createMock(
            GetEmailRecipients::class,
        );

        $get->method('get')->willReturnCallback(
            function (): EmailRecipientCollection {
                return $this->recipientCollection;
            }
        );

        return $get;
    }

    public function testSendWhenInvalid(): void
    {
        $formValues = new FormValues(
            fromUrl: '/test/from/url',
            redirectUrl: '/test/redirect/url',
            name: 'test name',
            email: 'foo@bar.baz',
            message: 'test message',
        );

        $result = $this->sendEmail->send(formValues: $formValues);

        self::assertFalse($result->sentSuccessfully());

        self::assertSame(
            ['recipients' => 'At least one recipient is required'],
            $result->formValues()->errorMessages(),
        );

        self::assertCount(2, $this->calls);

        self::assertSame(
            'TwigEnvironment',
            $this->calls[0]['object'],
        );

        self::assertSame(
            'render',
            $this->calls[0]['method'],
        );

        self::assertSame(
            '@app/Http/PageBuilder/BlockResponse/ContactForm/PostSubmission/SendEmail/EmailTemplatePlainText.twig',
            $this->calls[0]['name'],
        );

        $context = $this->calls[0]['context'];

        assert(is_array($context));

        self::assertCount(3, $context);

        self::assertSame(
            'test name',
            (string) $context['name'],
        );

        self::assertSame(
            'foo@bar.baz',
            (string) $context['emailAddress'],
        );

        self::assertSame(
            'test message',
            (string) $context['message'],
        );

        self::assertSame(
            'TwigEnvironment',
            $this->calls[1]['object'],
        );

        self::assertSame(
            'render',
            $this->calls[1]['method'],
        );

        self::assertSame(
            '@app/Http/PageBuilder/BlockResponse/ContactForm/PostSubmission/SendEmail/EmailTemplateHtml.twig',
            $this->calls[1]['name'],
        );

        $context2 = $this->calls[1]['context'];

        assert(is_array($context2));

        self::assertCount(3, $context2);

        self::assertSame(
            'test name',
            (string) $context2['name'],
        );

        self::assertSame(
            'foo@bar.baz',
            (string) $context2['emailAddress'],
        );

        self::assertSame(
            'test message',
            (string) $context2['message'],
        );
    }

    public function testSendWhenValid(): void
    {
        $this->recipientCollection = new EmailRecipientCollection(
            recipients: [
                new EmailRecipient(
                    'test-recipient@foobar.com',
                    'Test Recipient',
                ),
            ],
        );

        $formValues = new FormValues(
            fromUrl: '/test/from/url',
            redirectUrl: '/test/redirect/url',
            name: 'test name',
            email: 'foo@bar.baz',
            message: 'test message',
        );

        $result = $this->sendEmail->send(formValues: $formValues);

        self::assertTrue($result->sentSuccessfully());

        self::assertSame(
            [],
            $result->formValues()->errorMessages(),
        );

        self::assertCount(3, $this->calls);

        self::assertSame(
            'TwigEnvironment',
            $this->calls[0]['object'],
        );

        self::assertSame(
            'render',
            $this->calls[0]['method'],
        );

        self::assertSame(
            '@app/Http/PageBuilder/BlockResponse/ContactForm/PostSubmission/SendEmail/EmailTemplatePlainText.twig',
            $this->calls[0]['name'],
        );

        $context = $this->calls[0]['context'];

        assert(is_array($context));

        self::assertCount(3, $context);

        self::assertSame(
            'test name',
            (string) $context['name'],
        );

        self::assertSame(
            'foo@bar.baz',
            (string) $context['emailAddress'],
        );

        self::assertSame(
            'test message',
            (string) $context['message'],
        );

        self::assertSame(
            'TwigEnvironment',
            $this->calls[1]['object'],
        );

        self::assertSame(
            'render',
            $this->calls[1]['method'],
        );

        self::assertSame(
            '@app/Http/PageBuilder/BlockResponse/ContactForm/PostSubmission/SendEmail/EmailTemplateHtml.twig',
            $this->calls[1]['name'],
        );

        $context2 = $this->calls[1]['context'];

        assert(is_array($context2));

        self::assertCount(3, $context2);

        self::assertSame(
            'test name',
            (string) $context2['name'],
        );

        self::assertSame(
            'foo@bar.baz',
            (string) $context2['emailAddress'],
        );

        self::assertSame(
            'test message',
            (string) $context2['message'],
        );

        self::assertSame(
            'EmailApi',
            $this->calls[2]['object'],
        );

        self::assertSame(
            'enqueue',
            $this->calls[2]['method'],
        );

        $email = $this->calls[2]['email'];

        assert($email instanceof Email);

        self::assertSame(
            'St. Mark Website Contact Form',
            $email->subject()->toString(),
        );

        self::assertSame(
            'twigRenderResponse',
            $email->plaintext()->toString(),
        );

        self::assertSame(
            'twigRenderResponse',
            $email->html()->toString(),
        );

        self::assertSame(
            $this->recipientCollection,
            $email->recipients(),
        );

        self::assertSame(
            'foo@bar.baz',
            /** @phpstan-ignore-next-line */
            $email->from()->emailAddress()->toString(),
        );

        self::assertSame(
            'test name',
            /** @phpstan-ignore-next-line */
            $email->from()->name()->toString(),
        );
    }
}
