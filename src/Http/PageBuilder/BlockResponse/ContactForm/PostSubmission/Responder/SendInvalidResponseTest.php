<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\Responder;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\FormValues;
use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail\SendEmailResult;
use PHPUnit\Framework\TestCase;
use Slim\Flash\Messages as FlashMessages;
use Slim\Psr7\Factory\ResponseFactory;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 */
class SendInvalidResponseTest extends TestCase
{
    public function testRespond(): void
    {
        $result = new SendEmailResult(
            sentSuccessfully: true,
            formValues: new FormValues(
                fromUrl: '/test/from/url',
                redirectUrl: '/test/redirect/url',
                name: 'Test Name',
                email: 'test@foo.bar',
                message: 'test message',
            ),
        );

        $flashMessages = $this->createMock(
            FlashMessages::class,
        );

        $flashMessages->expects(self::once())
            ->method('addMessage')
            ->with(
                self::equalTo('ContactFormMessage'),
                self::equalTo($result),
            );

        $response = (new SendInvalidResponse(
            flashMessages: $flashMessages,
            responseFactory: new ResponseFactory(),
        ))->respond(result: $result);

        self::assertSame(
            303,
            $response->getStatusCode(),
        );

        self::assertSame(
            ['/test/from/url'],
            $response->getHeader('Location'),
        );
    }
}
