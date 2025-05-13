<?php

declare(strict_types=1);

namespace Cli;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Question\Question as SymfonyQuestion;

use function assert;
use function is_string;

readonly class CliQuestion
{
    public function __construct(
        private ArgvInput $input,
        private ConsoleOutput $output,
        private QuestionHelper $questionHelper,
    ) {
    }

    public function ask(
        string $question,
        bool $required = false,
        bool $hidden = false,
    ): string {
        $question = new SymfonyQuestion($question);

        $question->setHidden($hidden);

        $val = '';

        while ($val === '' || $val === null) {
            $val = $this->questionHelper->ask(
                $this->input,
                $this->output,
                $question,
            );

            if (! $required) {
                return is_string($val) ? $val : '';
            }

            assert(is_string($val) || $val === null);

            if ($val !== '' && $val !== null) {
                continue;
            }

            $this->output->writeln(
                '<fg=red>You must provide a value</>',
            );
        }

        return $val;
    }
}
