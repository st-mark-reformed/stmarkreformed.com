<?php

declare(strict_types=1);

namespace App\User\CLI;

use App\User\Result;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

use function array_map;

readonly class CliResultHandler
{
    public function __construct(private ConsoleOutputInterface $output)
    {
    }

    public function handleResult(
        Result $result,
        string $successMessage = 'Done!',
        string $errorMessage = 'Something went wrong:',
    ): int {
        if ($result->success) {
            $this->output->writeln(
                '<fg=green>' . $successMessage . '</>',
            );

            return 0;
        }

        $this->output->writeln(
            '<fg=red;options=bold>' . $errorMessage . '</>',
        );

        array_map(
            function (string $error): void {
                $this->output->writeln(
                    '<fg=red>' . $error . '</>',
                );
            },
            $result->errors,
        );

        return 1;
    }
}
