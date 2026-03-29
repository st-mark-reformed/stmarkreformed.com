<?php

declare(strict_types=1);

namespace App\DropdownList;

readonly class DropdownListEntity
{
    public function __construct(public string $value, public string $label)
    {
    }

    /**
     * @return array{
     *     value: string,
     *     label: string
     * }
     */
    public function asArray(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label,
        ];
    }
}
