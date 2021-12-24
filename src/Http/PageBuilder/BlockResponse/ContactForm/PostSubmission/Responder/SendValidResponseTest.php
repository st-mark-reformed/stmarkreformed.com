<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\Responder;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\FormValues;
use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail\SendEmailResult;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;

class SendValidResponseTest extends TestCase
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

        $response = (new SendValidResponse(
            responseFactory: new ResponseFactory(),
        ))->respond(result: $result);

        self::assertSame(
            303,
            $response->getStatusCode(),
        );

        self::assertSame(
            ['/test/redirect/url'],
            $response->getHeader('Location'),
        );
    }
}
