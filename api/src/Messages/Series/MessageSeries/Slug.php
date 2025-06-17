<?php

declare(strict_types=1);

namespace App\Messages\Series\MessageSeries;

use Assert\Assertion;
use Throwable;

use function array_map;
use function explode;
use function is_numeric;

readonly class Slug
{
    public bool $isValid;

    public string $errorMessage;

    public function __construct(public string $slug)
    {
        $errorMessage = '';

        try {
            Assertion::notEmpty(
                $slug,
                'A Slug is required',
            );
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();
        }

        if ($slug !== '') {
            try {
                $split = explode('-', $slug);

                array_map(
                    static function ($splitSeg): void {
                        if (is_numeric($splitSeg)) {
                            return;
                        }

                        Assertion::alnum(
                            $splitSeg,
                            'Slug must be alphanumeric with dashes',
                        );
                    },
                    $split,
                );
            } catch (Throwable $e) {
                $errorMessage = $e->getMessage();
            }
        }

        $this->isValid = $errorMessage === '';

        $this->errorMessage = $errorMessage;
    }
}
