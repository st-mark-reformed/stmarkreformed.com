<?php

declare(strict_types=1);

namespace App\Http\Response\News;

class GeneratePastorsPagesForRedis
{
    public function __construct(
        private GenerateNewsTypePagesForRedis $generateNewsTypePagesForRedis
    ) {
    }

    public function __invoke(): void
    {
        $this->generate();
    }

    public function generate(): void
    {
        $this->generateNewsTypePagesForRedis->generate('pastorsPage');
    }
}
