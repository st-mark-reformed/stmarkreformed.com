<?php

declare(strict_types=1);

namespace App\Messages\Series\Persistence;

use App\Messages\Series\MessageSeries\MessageSeries;
use App\Persistence\PersistRecord;
use App\Persistence\Result;

readonly class PersistFactory
{
    public function __construct(
        private FindBySlug $findBySlug,
        private Transformer $transformer,
        private PersistRecord $persistRecord,
    ) {
    }

    public function persist(MessageSeries $series): Result
    {
        if (! $series->isValid) {
            return new Result(
                false,
                $series->errorMessages,
            );
        }

        $existingSlug = $this->findBySlug->find($series->slug);

        if (
            $existingSlug !== null &&
            $existingSlug->id !== $series->id->toString()
        ) {
            return new Result(
                false,
                ['Specified slug already exists. Series slug must be unique'],
            );
        }

        $profileRecord = $this->transformer->createRecord($series);

        return $this->persistRecord->persist($profileRecord);
    }
}
