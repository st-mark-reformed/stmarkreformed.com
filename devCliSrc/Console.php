<?php

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
/** @noinspection PhpIllegalPsrClassPathInspection */

/**
 * For use before we've loaded composer and symfony console
 */

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
// phpcs:disable Generic.Formatting.MultipleStatementAlignment.NotSame

enum ConsoleForegroundColors: string
{
    case black = '0;30';
    case dark_gray = '1;30';
    case blue = '0;34';
    case light_blue = '1;34';
    case green = '0;32';
    case light_green = '1;32';
    case cyan = '0;36';
    case light_cyan = '1;36';
    case red = '0;31';
    case light_red = '1;31';
    case purple = '0;35';
    case light_purple = '1;35';
    case brown = '0;33';
    case yellow = '1;33';
    case light_gray = '0;37';
    case white = '1;37';
    case normal = '0;39';
}

enum ConsoleBackgroundColors: string
{
    case black = '40';
    case red = '41';
    case green = '42';
    case yellow = '43';
    case blue = '44';
    case magenta = '45';
    case cyan = '46';
    case light_gray = '47';
}

class Console
{
    public static function writeLn(
        string $message = '',
        ConsoleForegroundColors $color = ConsoleForegroundColors::normal,
        ConsoleBackgroundColors|null $backgroundColor = null,
    ): void {
        $styledOutput = "\033[";

        $styledOutput .= $color->value;

        $styledOutput .= 'm';

        if ($backgroundColor !== null) {
            $styledOutput .= "\033[";

            $styledOutput .= $backgroundColor->value;

            $styledOutput .= 'm';
        }

        $styledOutput .= $message;

        $styledOutput .= "\033[0m";

        echo $styledOutput . PHP_EOL;
    }
}
