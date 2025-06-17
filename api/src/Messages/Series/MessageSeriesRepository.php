<?php

declare(strict_types=1);

namespace App\Messages\Series;

use App\Messages\Series\MessageSeries\MessageSeries;
use App\Messages\Series\MessageSeries\MessageSeriesCollection;
use App\Messages\Series\MessageSeries\Slug;
use App\Messages\Series\Persistence\CreateAndPersistFactory;
use App\Messages\Series\Persistence\FindAll;
use App\Messages\Series\Persistence\FindBySlug;
use App\Messages\Series\Persistence\Transformer;
use App\Persistence\Result;

readonly class MessageSeriesRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindBySlug $findBySlug,
        private Transformer $transformer,
        private CreateAndPersistFactory $createAndPersistFactory,
    ) {
    }

    public function createAndPersist(MessageSeries $messageSeries): Result
    {
        return $this->createAndPersistFactory->create(
            $messageSeries,
        );
    }

    public function findAll(): MessageSeriesCollection
    {
        return $this->transformer->createMessageSeriesCollection(
            $this->findAll->find(),
        );
    }

    public function findBySlug(Slug $slug): MessageSeries|null
    {
        $record = $this->findBySlug->find($slug);

        if ($record === null) {
            return null;
        }

        return $this->transformer->createMessageSeries($record);
    }
}
