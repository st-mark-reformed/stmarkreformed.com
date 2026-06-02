<?php

declare(strict_types=1);

namespace App\News\Admin;

use App\News\NewsItems;
use App\Pagination\Pagination;
use JsonSerializable;

readonly class PaginatedNews implements JsonSerializable
{
    public function __construct(
        private NewsItems $newsItems,
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
            'entries' => $this->newsItems->asArray(),
        ];
    }
}
