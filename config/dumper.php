<?php

declare(strict_types=1);

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingAnyTypeHint
// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification

use Morrislaptop\VarDumperWithContext\HtmlDumper;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\ContextProvider\CliContextProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Dumper\ServerDumper;
use Symfony\Component\VarDumper\VarDumper;

$cloner = new VarCloner();

$htmlDumper = new class () extends HtmlDumper
{
    /** @phpstan-ignore-next-line */
    protected function getCaller()
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        foreach ($backtrace as $trace) {
            if (
                /** @phpstan-ignore-next-line */
                ! empty($trace['file']) &&
                /** @phpstan-ignore-next-line */
                ! empty($trace['line']) &&
                ! str_contains($trace['file'], '/vendor/') &&
                ! str_contains($trace['file'], 'dumper.php')
            ) {
                return $trace;
            }
        }

        return [];
    }

    /** @phpstan-ignore-next-line */
    public function dump(
        Data $data,
        $output = null,
        array $extraDisplayOptions = []
    ): void {
        $checkForTwigDumperFile = debug_backtrace()[3]['file'] ?? '';

        if (! $checkForTwigDumperFile) {
            $checkForTwigDumperFile = debug_backtrace()[4]['file'] ?? '';
        }

        $checkForTwigDumperArray = explode(
            DIRECTORY_SEPARATOR,
            $checkForTwigDumperFile,
        );

        $isTwigDumper = $checkForTwigDumperArray[count($checkForTwigDumperArray) - 1] === 'TwigDumper.php';

        if ($isTwigDumper) {
            echo '<div style="background-color: #fff; display: inline-block; margin: 10px; padding: 25px;">';
            parent::dump(
                $data,
                $output,
                $extraDisplayOptions,
            );
            echo '</div><br>';

            return;
        }

        parent::dump(
            $data,
            $output,
            $extraDisplayOptions,
        );
    }
};

$htmlDumper->setTheme('light');

$fallbackDumper = in_array(
    PHP_SAPI,
    ['cli', 'phpdbg'],
    true,
) ? new CliDumper() : $htmlDumper;

$contextProviders = [
    'cli' => new CliContextProvider(),
    'source' => new SourceContextProvider('UTF-8'),
];

$dumper = new ServerDumper(
    'tcp://127.0.0.1:9912',
    $fallbackDumper,
    $contextProviders,
);

VarDumper::setHandler(static function ($var) use ($cloner, $dumper): void {
    $dumper->dump($cloner->cloneVar($var));
});
