<?php

declare(strict_types=1);

namespace App\Shared\Testing;

use function debug_backtrace;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var mixed[] */
    protected array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];
    }

    /**
     * @param R $return
     *
     * @return R
     *
     * @template R
     */
    protected function genericCall(
        string $object,
        mixed $return = null
    ): mixed {
        $trace = debug_backtrace()[5];

        $this->calls[] = [
            'object' => $object,
            'method' => $trace['function'],
            'args' => $trace['args'],
        ];

        return $return;
    }
}
