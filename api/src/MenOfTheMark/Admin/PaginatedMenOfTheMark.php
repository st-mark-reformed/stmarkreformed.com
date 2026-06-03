<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Admin;

use App\MenOfTheMark\MenOfTheMarkItems;
use App\Pagination\Pagination;
use JsonSerializable;

readonly class PaginatedMenOfTheMark implements JsonSerializable
{
    public function __construct(
        private MenOfTheMarkItems $items,
        private Pagination $pagination,
    ) {
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return [
            'currentPage' => $this->pagination->currentPage(),
            'totalPages' => $this->pagination->totalPages(),
            'totalResults' => $this->pagination->totalResults(),
            'entries' => $this->items->asArray(),
        ];
    }
}
