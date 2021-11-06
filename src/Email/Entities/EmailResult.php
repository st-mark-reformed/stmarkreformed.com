<?php

declare(strict_types=1);

namespace App\Email\Entities;

class EmailResult
{
    public function __construct(private bool $sentSuccessfully)
    {
    }

    public function sentSuccessfully(): bool
    {
        return $this->sentSuccessfully;
    }
}
