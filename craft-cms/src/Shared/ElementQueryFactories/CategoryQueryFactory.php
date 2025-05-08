<?php

declare(strict_types=1);

namespace App\Shared\ElementQueryFactories;

use craft\elements\Category;
use craft\elements\db\CategoryQuery;

/**
 * @codeCoverageIgnore
 */
class CategoryQueryFactory
{
    /**
     * @phpstan-ignore-next-line
     */
    public function make(): CategoryQuery
    {
        return Category::find();
    }
}
