<?php

declare(strict_types=1);

namespace App\Messages;

use App\Messages\Message\Messages;
use App\Pagination;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class FindByLimitResults
{
    public function __construct(
        public int $limit,
        public int $offset,
        public int $absoluteTotalResults,
        public Messages $messages,
    ) {
    }

    /** @phpstan-ignore-next-line */
    public function asScalarWithPageData(int $currentPage): array
    {
        $pagination = (new Pagination())
            ->withPerPage($this->limit)
            ->withCurrentPage($currentPage)
            ->withTotalResults($this->absoluteTotalResults);

        return [
            'currentPage' => $pagination->currentPage(),
            'perPage' => $pagination->perPage(),
            'totalResults' => $pagination->totalResults(),
            'totalPages' => $pagination->totalPages(),
            'pagesArray' => $pagination->pagesArray(),
            'prevPageLink' => $pagination->prevPageLink(),
            'nextPageLink' => $pagination->nextPageLink(),
            'firstPageLink' => $pagination->firstPageLink(),
            'lastPageLink' => $pagination->lastPageLink(),
            'messages' => $this->messages->asScalar(),
        ];
    }
}
