<?php

declare(strict_types=1);

namespace App\Messages\Console;

use App\Messages\MessagesApi;
use App\Shared\Testing\TestCase;
use BuzzingPixel\CraftScheduler\Cli\Services\Output;
use PHPUnit\Framework\MockObject\MockObject;
use yii\helpers\BaseConsole;

class SetMessageSeriesLatestEntryCommandTest extends TestCase
{
    private SetMessageSeriesLatestEntryCommand $command;

    /** @var Output&MockObject */
    private mixed $output;

    protected function setUp(): void
    {
        parent::setUp();

        $this->output = $this->mockOutput();

        $this->command = new SetMessageSeriesLatestEntryCommand(
            messagesApi: $this->mockMessagesApi(),
        );
    }

    /**
     * @return MockObject&Output
     */
    private function mockOutput(): mixed
    {
        $output = $this->createMock(Output::class);

        $output->method('writeln')->willReturnCallback(
            function (): void {
                $this->genericCall(object: 'Output');
            }
        );

        return $output;
    }

    /**
     * @return MessagesApi&MockObject
     */
    private function mockMessagesApi(): mixed
    {
        $api = $this->createMock(MessagesApi::class);

        $api->method('setMessageSeriesLatestEntry')
            ->willReturnCallback(function (): void {
                $this->genericCall(object: 'MessagesApi');
            });

        return $api;
    }

    public function testRun(): void
    {
        $this->command->run(output: $this->output);

        self::assertSame(
            [
                [
                    'object' => 'Output',
                    'method' => 'writeln',
                    'args' => [
                        'Setting messages series latest entry dates...',
                        BaseConsole::FG_YELLOW,
                    ],
                ],
                [
                    'object' => 'MessagesApi',
                    'method' => 'setMessageSeriesLatestEntry',
                    'args' => [],
                ],
                [
                    'object' => 'Output',
                    'method' => 'writeln',
                    'args' => [
                        'Finished setting messages series latest entry dates.',
                        BaseConsole::FG_GREEN,
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
