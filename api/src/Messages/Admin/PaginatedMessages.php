<?php

declare(strict_types=1);

namespace App\Messages\Admin;

use App\Messages\Messages;
use App\Pagination\Pagination;
use JsonSerializable;

readonly class PaginatedMessages implements JsonSerializable
{
    public function __construct(
        private Messages $messages,
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
            'entries' => $this->messages->asArray(),
        ];
    }
}
