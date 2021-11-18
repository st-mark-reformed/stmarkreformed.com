<?php

declare(strict_types=1);

namespace App\Messages\Console;

use App\Messages\MessagesApi;
use BuzzingPixel\CraftScheduler\Cli\Services\Output;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use yii\helpers\BaseConsole;

use function debug_backtrace;

class SetMessageSeriesLatestEntryCommandTest extends TestCase
{
    private SetMessageSeriesLatestEntryCommand $command;

    /** @var mixed[] */
    private array $calls = [];

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
     * @param R $return
     *
     * @return R
     *
     * @template R
     */
    private function genericCall(
        string $object,
        mixed $return = null
    ): mixed {
        $trace = debug_backtrace()[5];

        $this->calls[] = [
            'object' => $object,
            'method' => $trace['function'],
            'args' => $trace['args'],
        ];

        return $return;
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
