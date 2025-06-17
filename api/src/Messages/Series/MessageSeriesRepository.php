<?php

declare(strict_types=1);

namespace App\Messages\Series;

use App\Messages\Series\MessageSeries\MessageSeries;
use App\Messages\Series\Persistence\CreateAndPersistFactory;
use App\Persistence\Result;

readonly class MessageSeriesRepository
{
    public function __construct(
        private CreateAndPersistFactory $createAndPersistFactory,
    ) {
    }

    public function createAndPersist(MessageSeries $messageSeries): Result
    {
        return $this->createAndPersistFactory->create(
            $messageSeries,
        );
    }
}
