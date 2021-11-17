<?php

declare(strict_types=1);

namespace App\Profiles\Console;

use App\Profiles\ProfilesApi;
use BuzzingPixel\CraftScheduler\Cli\Services\Output;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use yii\helpers\BaseConsole;

use function debug_backtrace;

/**
 * @psalm-suppress MissingClosureParamType
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress PropertyNotSetInConstructor
 */
class SetHasMessagesOnAllProfilesCommandTest extends TestCase
{
    private SetHasMessagesOnAllProfilesCommand $command;

    /** @var mixed[] */
    private array $calls = [];

    /** @var Output&MockObject */
    private mixed $output;

    protected function setUp(): void
    {
        parent::setUp();

        $this->output = $this->mockOutput();

        $this->command = new SetHasMessagesOnAllProfilesCommand(
            profilesApi: $this->mockProfilesApi(),
        );
    }

    /**
     * @param R $return
     *
     * @return R
     *
     * @template R
     * @psalm-suppress PossiblyUndefinedArrayOffset
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
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
     * @return ProfilesApi&MockObject
     */
    private function mockProfilesApi(): mixed
    {
        $api = $this->createMock(ProfilesApi::class);

        $api->method(self::anything())
            ->willReturnCallback(function (): void {
                $this->genericCall(object: 'ProfilesApi');
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
                        'Updating all profiles...',
                        BaseConsole::FG_YELLOW,
                    ],
                ],
                [
                    'object' => 'ProfilesApi',
                    'method' => 'setHasMessagesOnAllProfiles',
                    'args' => [],
                ],
                [
                    'object' => 'Output',
                    'method' => 'writeln',
                    'args' => [
                        'Finished updating all profiles.',
                        BaseConsole::FG_GREEN,
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
