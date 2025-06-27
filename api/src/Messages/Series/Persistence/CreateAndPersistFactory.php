<?php

declare(strict_types=1);

namespace App\Messages\Series\Persistence;

use App\Messages\Series\MessageSeries\MessageSeries;
use App\Persistence\PersistNewRecord;
use App\Persistence\PersistRecord;
use App\Persistence\Result;

readonly class CreateAndPersistFactory
{
    public function __construct(
        private FindBySlug $findBySlug,
        private Transformer $transformer,
        private PersistRecord $persistRecord,
        private PersistNewRecord $persistNewRecord,
    ) {
    }

    public function create(MessageSeries $series): Result
    {
        $series = $this->validate($series);

        if (! $series->isValid) {
            return new Result(
                false,
                $series->errorMessages,
            );
        }

        $record = $this->transformer->createRecord($series);

        return $this->persistNewRecord->persist($record);
    }

    public function persist(MessageSeries $series): Result
    {
        $series = $this->validate($series);

        if (! $series->isValid) {
            return new Result(
                false,
                $series->errorMessages,
            );
        }

        $profileRecord = $this->transformer->createRecord($series);

        return $this->persistRecord->persist($profileRecord);
    }

    private function validate(MessageSeries $series): MessageSeries
    {
        $existingSlug = $this->findBySlug->find(
            $series->slug,
            $series->id,
        );

        if ($existingSlug !== null) {
            $series = $series->withErrorMessage(
                'Specified slug already exists. Series slug must be unique',
            );
        }

        return $series;
    }
}
