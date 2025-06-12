<?php

declare(strict_types=1);

namespace App\Persistence;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class Result
{
    /** @param string[] $messages */
    public function __construct(
        public bool $success,
        public array $messages,
        public int|null $httpResponseCode = null,
    ) {
    }

    /** @phpstan-ignore-next-line */
    public function asScalarArray(): array
    {
        return [
            'success' => $this->success,
            'messages' => $this->messages,
        ];
    }
}
