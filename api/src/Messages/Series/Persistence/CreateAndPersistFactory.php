<?php

declare(strict_types=1);

namespace App\Messages\Series\Persistence;

use App\Messages\Series\MessageSeries\MessageSeries;
use App\Persistence\PersistNewRecord;
use App\Persistence\Result;

readonly class CreateAndPersistFactory
{
    public function __construct(
        private Transformer $transformer,
        private PersistNewRecord $persistNewRecord,
    ) {
    }

    public function create(MessageSeries $messageSeries): Result
    {
        if (! $messageSeries->isValid) {
            return new Result(
                false,
                $messageSeries->errorMessages,
            );
        }

        // TODO: Validate series is unique

        $record = $this->transformer->createRecord($messageSeries);

        return $this->persistNewRecord->persist($record);
    }
}
