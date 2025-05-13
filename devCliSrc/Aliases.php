<?php

declare(strict_types=1);

namespace Cli;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Throwable;

use function array_filter;
use function array_keys;
use function array_map;
use function array_slice;
use function array_splice;
use function array_values;
use function call_user_func_array;
use function count;
use function explode;
use function implode;
use function is_callable;

readonly class Aliases
{
    /** @var Alias[] */
    private array $items;

    public function __construct()
    {
        $generator = new ClassMapGenerator();
        $generator->scanPaths(__DIR__ . '/Commands');

        $this->items = array_values(array_filter(
            array_map(
                [$this, 'createAliasFromClassString'],
                array_keys($generator->getClassMap()->map),
            ),
            static fn (Alias|null $i) => $i !== null,
        ));
    }

    private function createAliasFromClassString(string $classString): Alias|null
    {
        try {
            if (! is_callable($classString . '::applyCommand')) {
                return null;
            }

            $event = new ApplyCliCommandsEventForAliases();

            call_user_func_array(
                $classString . '::applyCommand',
                [$event],
            );

            $expression = explode(
                ' ',
                $event->expression->expression,
            )[0];

            return new Alias($expression);
        } catch (Throwable) {
            return null;
        }
    }

    public function map(): void
    {
        foreach ($this->items as $item) {
            if (! $this->mapItem($item)) {
                continue;
            }

            break;
        }
    }

    private function mapItem(Alias $alias): bool
    {
        $args = $_SERVER['argv'];

        $relevantArgs = array_slice(
            $args,
            1,
            $alias->toCount(),
        );

        $relevantArgsString = implode(':', $relevantArgs);

        if ($relevantArgsString !== $alias->from) {
            return false;
        }

        array_splice(
            $args,
            1,
            $alias->toCount(),
            $alias->from,
        );

        $_SERVER['argv'] = $args;

        $_SERVER['argc'] = count($args);

        return true;
    }
}
