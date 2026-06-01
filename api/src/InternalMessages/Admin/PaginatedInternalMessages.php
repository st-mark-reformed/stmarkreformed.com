<?php

declare(strict_types=1);

namespace App\InternalMessages\Admin;

use App\InternalMessages\InternalMessages;
use App\Pagination\Pagination;
use JsonSerializable;

readonly class PaginatedInternalMessages implements JsonSerializable
{
    public function __construct(
        private InternalMessages $messages,
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
