<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth\Response;

use App\Http\Response\Members\HymnsOfTheMonth\HymnResults;

class HymnsOfTheMonthResponderFactory
{
    public function __construct(
        private RespondWithResults $respondWithResults,
        private RespondWithNoResults $respondWithNoResults,
    ) {
    }

    public function make(HymnResults $results): HymnsOfTheMonthResponderContract
    {
        if (! $results->hasResults()) {
            return $this->respondWithNoResults;
        }

        return $this->respondWithResults;
    }
}
