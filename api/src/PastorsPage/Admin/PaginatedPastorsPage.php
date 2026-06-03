<?php

declare(strict_types=1);

namespace App\PastorsPage\Admin;

use App\Pagination\Pagination;
use App\PastorsPage\PastorsPageItems;
use JsonSerializable;

readonly class PaginatedPastorsPage implements JsonSerializable
{
    public function __construct(
        private PastorsPageItems $pastorsPageItems,
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
            'entries' => $this->pastorsPageItems->asArray(),
        ];
    }
}
