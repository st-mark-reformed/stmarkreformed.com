<?php

declare(strict_types=1);

namespace App\Http\Response\LogIn\Response;

use App\Http\Response\LogIn\LogInPayload;
use App\Shared\Testing\TestCase;

class LogInResponderFactoryTest extends TestCase
{
    private RespondWithError $respondWithError;

    private RespondWithSuccess $respondWithSuccess;

    private LogInResponderFactory $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->respondWithError = $this->createMock(
            RespondWithError::class,
        );

        $this->respondWithSuccess = $this->createMock(
            RespondWithSuccess::class,
        );

        $this->responder = new LogInResponderFactory(
            respondWithError: $this->respondWithError,
            respondWithSuccess: $this->respondWithSuccess,
        );
    }

    public function testMakeWhenSucceeded(): void
    {
        self::assertSame(
            $this->responder->make(payload: new LogInPayload(
                succeeded: true,
            )),
            $this->respondWithSuccess,
        );
    }

    public function testMakeWhenFailed(): void
    {
        self::assertSame(
            $this->responder->make(payload: new LogInPayload(
                succeeded: false,
            )),
            $this->respondWithError,
        );
    }
}
