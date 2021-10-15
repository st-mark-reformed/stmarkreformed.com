<?php

declare(strict_types=1);

namespace App\Craft\SetMessageEntrySlug\Services;

use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class DoNotSetSlugTest extends TestCase
{
    public function testSet(): void
    {
        $doNotSetSlug = new DoNotSetSlug();

        $doNotSetSlug->set();

        self::assertTrue(true);
    }
}
