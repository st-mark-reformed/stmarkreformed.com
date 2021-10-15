<?php

declare(strict_types=1);

namespace App\Craft\SetMessageEntrySlug\Services;

class DoNotSetSlug implements SetMessageEntrySlugContract
{
    public function set(): void
    {
    }
}
