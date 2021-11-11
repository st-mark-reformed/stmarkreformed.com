<?php

declare(strict_types=1);

namespace App\ElasticSearch\Console;

use App\ElasticSearch\ElasticSearchApi;
use BuzzingPixel\CraftScheduler\Cli\Services\Output;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use yii\helpers\BaseConsole;

/**
 * @psalm-suppress MissingClosureParamType
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress PropertyNotSetInConstructor
 */
class IndexAllMessagesCommandTest extends TestCase
{
    private IndexAllMessagesCommand $indexAllMessagesCommand;

    /** @var mixed[] */
    private array $calls = [];
    /** @var Output&MockObject */
    private mixed $output;

    protected function setUp(): void
    {
        parent::setUp();

        $this->output = $this->mockOutput();

        $this->indexAllMessagesCommand = new IndexAllMessagesCommand(
            elasticSearchApi: $this->mockElasticSearchApi(),
        );
    }

    /**
     * @return ElasticSearchApi&MockObject
     */
    private function mockElasticSearchApi(): mixed
    {
        $elasticSearchApi = $this->createMock(
            ElasticSearchApi::class,
        );

        $elasticSearchApi->method('indexAllMessages')
            ->willReturnCallback(
                function (): void {
                    $this->calls[] = [
                        'object' => 'ElasticSearchApi',
                        'method' => 'indexAllMessages',
                    ];
                }
            );

        return $elasticSearchApi;
    }

    /**
     * @return MockObject&Output
     */
    private function mockOutput(): mixed
    {
        $output = $this->createMock(Output::class);

        $output->method('writeln')->willReturnCallback(
            function (string $message, ...$decorations): void {
                $this->calls[] = [
                    'object' => 'Output',
                    'method' => 'writeln',
                    'message' => $message,
                    'decorations' => $decorations,
                ];
            }
        );

        return $output;
    }

    public function testRun(): void
    {
        $this->indexAllMessagesCommand->run(output: $this->output);

        self::assertSame(
            [
                [
                    'object' => 'Output',
                    'method' => 'writeln',
                    'message' => 'Indexing all messages...',
                    'decorations' => [BaseConsole::FG_YELLOW],
                ],
                [
                    'object' => 'ElasticSearchApi',
                    'method' => 'indexAllMessages',
                ],
                [
                    'object' => 'Output',
                    'method' => 'writeln',
                    'message' => 'Finished indexing messages.',
                    'decorations' => [BaseConsole::FG_GREEN],
                ],
            ],
            $this->calls,
        );
    }
}
