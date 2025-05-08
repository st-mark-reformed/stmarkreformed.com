<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\Responder;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail\SendEmailResult;
use PHPUnit\Framework\TestCase;

class ResponderFactoryTest extends TestCase
{
    private ResponderFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ResponderFactory(
            sendValidResponse: $this->createMock(
                SendValidResponse::class,
            ),
            sendInvalidResponse: $this->createMock(
                SendInvalidResponse::class,
            ),
        );
    }

    public function testMakeWhenSuccess(): void
    {
        $result = $this->createMock(SendEmailResult::class);

        $result->method('sentSuccessfully')->willReturn(true);

        self::assertInstanceOf(
            SendValidResponse::class,
            $this->factory->make(result: $result),
        );
    }

    public function testMakeWhenNotSuccess(): void
    {
        $result = $this->createMock(SendEmailResult::class);

        $result->method('sentSuccessfully')->willReturn(false);

        self::assertInstanceOf(
            SendInvalidResponse::class,
            $this->factory->make(result: $result),
        );
    }
}
