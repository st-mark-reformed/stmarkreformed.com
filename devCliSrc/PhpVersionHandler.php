<?php

declare(strict_types=1);

namespace Cli;

use Mistralys\VersionParser\VersionParser;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

use function exec;
use function file_get_contents;
use function implode;
use function json_decode;
use function version_compare;

use const PHP_EOL;

readonly class PhpVersionHandler
{
    public function __construct(private ConsoleOutputInterface $output)
    {
    }

    public function warnIfWrongVersion(): void
    {
        $cliVer = $this->getCliPhpVersion();

        $cli = $cliVer->getMajorVersion() . '.' . $cliVer->getMinorVersion();

        $appVer = $this->getAppPhpVersion();

        $app = $appVer->getMajorVersion() . '.' . $appVer->getMinorVersion();

        if (! version_compare($cli, $app, '<')) {
            return;
        }

        $outputStyle = new OutputFormatterStyle('white', 'red', ['bold']);

        $this->output->getFormatter()->setStyle('error', $outputStyle);

        $this->output->writeln(implode(PHP_EOL . '  ', [
            '<error>',
            '',
            'Your CLI PHP version: ' . $cliVer->getOriginalString() . ', is not new enough. Despite our best efforts to get PHPStorm to use the correct version of PHP despite when your CLI is set to, PHPStorm still throws a fit if the CLI version is not correct. Run the following command to switch, then run this command again:',
            '</error>',
        ]));

        $this->output->writeln('');

        $this->output->writeln('<fg=cyan>    brew unlink php@8.2 && brew link --force --overwrite shivammathur/php/php@8.4</>');

        $this->output->writeln('');

        exit;
    }

    private function getCliPhpVersion(): VersionParser
    {
        exec(
            'php -r "echo PHP_VERSION;"',
            $cliPhpVersionString,
        );

        $cliPhpVersionString = implode(
            PHP_EOL,
            $cliPhpVersionString,
        );

        return VersionParser::create($cliPhpVersionString);
    }

    private function getAppPhpVersion(): VersionParser
    {
        $composerJson = json_decode(
            file_get_contents(__DIR__ . '/composer.json'),
            true,
        );

        return VersionParser::create(VersionParser::create(
            $composerJson['require']['php'],
        )->getBranchName());
    }
}
