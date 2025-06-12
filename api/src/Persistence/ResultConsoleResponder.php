<?php

declare(strict_types=1);

namespace App\Persistence;

use Symfony\Component\Console\Output\ConsoleOutput;

use function array_map;
use function implode;

readonly class ResultConsoleResponder
{
    public function __construct(private ConsoleOutput $output)
    {
    }

    public function respond(
        Result $result,
        string $successMessage,
        string $failureMessage,
    ): void {
        if ($result->success) {
            $this->output->writeln(
                '<fg=green>' . $successMessage . '</>',
            );

            return;
        }

        $this->output->writeln('<fg=red>' . $failureMessage . '</>');

        array_map(
            function (string $msg): void {
                $this->output->writeln(implode('', [
                    '<fg=red>',
                    $msg,
                    '</>',
                ]));
            },
            $result->messages,
        );
    }
}
