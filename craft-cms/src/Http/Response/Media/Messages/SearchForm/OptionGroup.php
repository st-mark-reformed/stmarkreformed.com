<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\SearchForm;

use function array_map;

class OptionGroup
{
    /** @var SelectOption[] */
    private array $options = [];

    /**
     * @param SelectOption[] $selectOptions
     */
    public function __construct(
        private string $groupTitle,
        array $selectOptions,
    ) {
        array_map(
            function (SelectOption $option): void {
                $this->options[] = $option;
            },
            $selectOptions,
        );
    }

    public function groupTitle(): string
    {
        return $this->groupTitle;
    }

    /**
     * @return SelectOption[]
     */
    public function options(): array
    {
        return $this->options;
    }

    /**
     * @return mixed[]
     */
    public function map(callable $callable): array
    {
        return array_map(
            $callable,
            $this->options(),
        );
    }
}
