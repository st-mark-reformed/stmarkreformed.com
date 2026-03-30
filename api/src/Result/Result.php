<?php

declare(strict_types=1);

namespace App\Result;

use Exception;
use Throwable;

use function implode;

class Result extends Exception implements Throwable
{
    /** @param string[] $errors */
    public function __construct(
        public readonly bool $success = true,
        public readonly array $errors = [],
    ) {
        parent::__construct(implode(', ', $errors));
    }

    /**
     * @return array{
     *     success: bool,
     *     errors: string[],
     * }
     */
    public function asArray(): array
    {
        return [
            'success' => $this->success,
            'errors' => $this->errors,
        ];
    }
}
