<?php

declare(strict_types=1);

namespace App\Http\Response\LogIn\Response;

use App\Http\Response\LogIn\LogInPayload;
use App\Shared\Testing\MockFlashMessagesForTesting;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\TestCase;

class RespondWithErrorTest extends TestCase
{
    use MockFlashMessagesForTesting;
    use MockResponseFactoryForTesting;

    private RespondWithError $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = new RespondWithError(
            flashMessages: $this->mockFlashMessages(),
            responseFactory: $this->mockResponseFactory(),
        );
    }

    public function testRespond(): void
    {
        $payload = new LogInPayload(succeeded: true);

        $response = $this->responder->respond(
            redirectTo: '/test/redirect/to',
            payload: $payload,
        );

        self::assertSame($this->response, $response);

        self::assertSame(
            [
                [
                    'object' => 'FlashMessages',
                    'method' => 'addMessage',
                    'args' => [
                        'FormMessage',
                        [
                            'status' => 'error',
                            'message' => '',
                        ],
                    ],
                ],
                [
                    'object' => 'ResponseFactoryInterface',
                    'method' => 'createResponse',
                    'args' => [303],
                ],
                [
                    'object' => 'ResponseInterface',
                    'method' => 'withHeader',
                    'args' => [
                        'Location',
                        '/test/redirect/to',
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
