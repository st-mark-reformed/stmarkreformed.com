<?php

declare(strict_types=1);

namespace App\Images\Queue;

use craft\queue\BaseJob;
use craft\queue\Queue;
use PHPUnit\Framework\TestCase;

class PushToQueueIfNotInQueueTest extends TestCase
{
    private PushToQueueIfNotInQueue $pusher;

    /** @var BaseJob[] */
    private array $queueJobsInQueue = [];

    /** @var mixed[] */
    private array $queueCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->queueJobsInQueue = [];

        $queueStub = $this->createMock(Queue::class);

        $queueStub->method('getJobInfo')->willReturnCallback(
            function (): array {
                return $this->queueJobsInQueue;
            }
        );

        $queueStub->method('push')->willReturnCallback(
            function (BaseJob $job): bool {
                $this->queueCalls[] = [
                    'method' => 'push',
                    'job' => $job,
                ];

                return true;
            }
        );

        $this->pusher = new PushToQueueIfNotInQueue(queue: $queueStub);
    }

    public function testPushWhenJobIsInQueue(): void
    {
        /** @phpstan-ignore-next-line */
        $this->queueJobsInQueue = [
            ['description' => 'testDesc1'],
            ['description' => 'testDesc2'],
            ['description' => 'testDesc3'],
        ];

        $job = $this->createMock(BaseJob::class);

        $job->method('getDescription')->willReturn(
            'testDesc2'
        );

        $this->pusher->push($job);

        self::assertSame([], $this->queueCalls);
    }

    public function testPush(): void
    {
        /** @phpstan-ignore-next-line */
        $this->queueJobsInQueue = [
            ['description' => 'testDesc1'],
            ['description' => 'testDesc2'],
            ['description' => 'testDesc3'],
        ];

        $job = $this->createMock(BaseJob::class);

        $job->method('getDescription')->willReturn(
            'food'
        );

        $this->pusher->push($job);

        self::assertSame(
            [
                [
                    'method' => 'push',
                    'job' => $job,
                ],
            ],
            $this->queueCalls,
        );
    }
}
