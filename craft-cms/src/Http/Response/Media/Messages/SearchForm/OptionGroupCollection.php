<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\SearchForm;

use function array_map;

class OptionGroupCollection
{
    /** @var OptionGroup[] */
    private array $optionGroups = [];

    /**
     * @param OptionGroup[] $optionGroups
     */
    public function __construct(array $optionGroups)
    {
        array_map(
            function (OptionGroup $optionGroup): void {
                $this->optionGroups[] = $optionGroup;
            },
            $optionGroups,
        );
    }

    /**
     * @return OptionGroup[]
     */
    public function optionGroups(): array
    {
        return $this->optionGroups;
    }

    /**
     * @return mixed[]
     */
    public function map(callable $callable): array
    {
        return array_map(
            $callable,
            $this->optionGroups(),
        );
    }
}
