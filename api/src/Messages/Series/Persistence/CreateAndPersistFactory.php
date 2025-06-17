<?php

declare(strict_types=1);

namespace App\Messages\Series\Persistence;

use App\Messages\Series\MessageSeries\MessageSeries;
use App\Persistence\PersistNewRecord;
use App\Persistence\Result;

readonly class CreateAndPersistFactory
{
    public function __construct(
        private FindBySlug $findBySlug,
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

        $existingSlug = $this->findBySlug->find($messageSeries->slug);

        if ($existingSlug !== null) {
            return new Result(
                false,
                ['Specified slug already exists. Message slug must be unique'],
            );
        }

        $record = $this->transformer->createRecord($messageSeries);

        return $this->persistNewRecord->persist($record);
    }
}
