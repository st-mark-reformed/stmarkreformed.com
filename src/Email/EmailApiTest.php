<?php

declare(strict_types=1);

namespace App\Email;

use App\Email\Entities\Email;
use App\Email\Entities\EmailResult;
use App\Email\Queue\SendEmailQueueJob;
use craft\queue\BaseJob;
use craft\queue\Queue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class EmailApiTest extends TestCase
{
    private EmailApi $emailApi;

    /** @var mixed[] */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->emailApi = new EmailApi(
            queue: $this->mockQueue(),
            sendMail: $this->mockSendMail(),
        );
    }

    /**
     * @return MockObject&Queue
     */
    private function mockQueue(): mixed
    {
        $queue = $this->createMock(
            Queue::class,
        );

        $queue->method('push')->willReturnCallback(
            function (BaseJob $job): string {
                $this->calls[] = [
                    'object' => 'Queue',
                    'method' => 'push',
                    'job' => $job,
                ];

                return 'foo-bar';
            }
        );

        return $queue;
    }

    /**
     * @return MockObject&SendMailContract
     */
    private function mockSendMail(): mixed
    {
        $sendMail = $this->createMock(
            SendMailContract::class,
        );

        $sendMail->method('send')->willReturnCallback(
            function (Email $email): EmailResult {
                $this->calls[] = [
                    'object' => 'SendMailContract',
                    'method' => 'send',
                    'email' => $email,
                ];

                return new EmailResult(sentSuccessfully: true);
            }
        );

        return $sendMail;
    }

    public function testSend(): void
    {
        $email = $this->createMock(Email::class);

        $result = $this->emailApi->send(email: $email);

        self::assertTrue($result->sentSuccessfully());

        self::assertSame(
            [
                [
                    'object' => 'SendMailContract',
                    'method' => 'send',
                    'email' => $email,
                ],
            ],
            $this->calls,
        );
    }

    public function testEnqueue(): void
    {
        $email = $this->createMock(Email::class);

        $this->emailApi->enqueue(email: $email);

        self::assertCount(1, $this->calls);

        self::assertSame(
            'Queue',
            $this->calls[0]['object'],
        );

        self::assertSame(
            'push',
            $this->calls[0]['method'],
        );

        $job = $this->calls[0]['job'];

        assert($job instanceof SendEmailQueueJob);

        $jobReflection = new ReflectionClass($job);

        $emailProperty = $jobReflection->getProperty('email');

        $emailProperty->setAccessible(true);

        self::assertSame(
            $email,
            $emailProperty->getValue($job),
        );
    }
}
