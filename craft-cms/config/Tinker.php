<?php

declare(strict_types=1);

namespace Config;

class Tinker
{
    public function __construct(
    ) {
    }

    public function __invoke(): void
    {
        echo 'here';
        die;
    }
}
