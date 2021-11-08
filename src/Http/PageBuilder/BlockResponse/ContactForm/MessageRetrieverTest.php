<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\FormValues;
use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail\SendEmailResult;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Slim\Flash\Messages as FlashMessages;

use function assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 */
class MessageRetrieverTest extends TestCase
{
    private MessageRetriever $messageRetriever;

    /** @var mixed[] */
    private array $calls = [];

    private bool $messageReturnsArray = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->messageReturnsArray = false;

        $this->messageRetriever = new MessageRetriever(
            messages: $this->mockMessages(),
        );
    }

    /**
     * @return MockObject&FlashMessages
     */
    private function mockMessages(): mixed
    {
        $messages = $this->createMock(
            FlashMessages::class,
        );

        $messages->method('getMessage')->willReturnCallback(
            function (string $key): ?array {
                $this->calls[] = [
                    'object' => 'FlashMessages',
                    'method' => 'getMessage',
                    'key' => $key,
                ];

                if ($this->messageReturnsArray) {
                    $formValues = $this->createMock(
                        FormValues::class,
                    );

                    $formValues->method('nameRaw')->willReturn(
                        'testNameValue',
                    );

                    $result = new SendEmailResult(
                        sentSuccessfully: true,
                        formValues: $formValues,
                    );

                    return [$result];
                }

                return null;
            }
        );

        return $messages;
    }

    public function testRetrieveFormValuesFromMessageWhenMessageIsNull(): void
    {
        $this->messageReturnsArray = false;

        self::assertNull(
            $this->messageRetriever->retrieveFormValuesFromMessage(),
        );

        self::assertSame(
            [
                [
                    'object' => 'FlashMessages',
                    'method' => 'getMessage',
                    'key' => 'ContactFormMessage',
                ],
            ],
            $this->calls,
        );
    }

    public function testRetrieveFormValuesFromMessage(): void
    {
        $this->messageReturnsArray = true;

        $formValues = $this->messageRetriever->retrieveFormValuesFromMessage();

        assert($formValues instanceof FormValues);

        self::assertSame(
            'testNameValue',
            $formValues->nameRaw(),
        );

        self::assertSame(
            [
                [
                    'object' => 'FlashMessages',
                    'method' => 'getMessage',
                    'key' => 'ContactFormMessage',
                ],
            ],
            $this->calls,
        );
    }
}
