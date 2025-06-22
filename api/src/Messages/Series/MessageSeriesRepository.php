<?php

declare(strict_types=1);

namespace App\Messages\Series;

use App\Messages\Series\MessageSeries\MessageSeries;
use App\Messages\Series\MessageSeries\MessageSeriesCollection;
use App\Messages\Series\MessageSeries\Slug;
use App\Messages\Series\Persistence\CreateAndPersistFactory;
use App\Messages\Series\Persistence\DeleteIds;
use App\Messages\Series\Persistence\FindAll;
use App\Messages\Series\Persistence\FindById;
use App\Messages\Series\Persistence\FindByIds;
use App\Messages\Series\Persistence\FindBySlug;
use App\Messages\Series\Persistence\PersistFactory;
use App\Messages\Series\Persistence\Transformer;
use App\Persistence\Result;
use App\Persistence\UuidCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

use function is_string;

readonly class MessageSeriesRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private DeleteIds $deleteIds,
        private FindByIds $findByIds,
        private FindBySlug $findBySlug,
        private Transformer $transformer,
        private PersistFactory $persistFactory,
        private CreateAndPersistFactory $createAndPersistFactory,
    ) {
    }

    public function createAndPersist(MessageSeries $messageSeries): Result
    {
        return $this->createAndPersistFactory->create(
            $messageSeries,
        );
    }

    public function persist(MessageSeries $messageSeries): Result
    {
        return $this->persistFactory->persist($messageSeries);
    }

    public function findAll(): MessageSeriesCollection
    {
        return $this->transformer->createMessageSeriesCollection(
            $this->findAll->find(),
        );
    }

    /** @param string[] $ids */
    public function findByIds(array $ids): MessageSeriesCollection
    {
        return $this->transformer->createMessageSeriesCollection(
            $this->findByIds->find($ids),
        );
    }

    public function findById(UuidInterface|string $id): MessageSeries|null
    {
        if (is_string($id)) {
            $id = Uuid::fromString($id);
        }

        $record = $this->findById->find($id);

        if ($record === null) {
            return null;
        }

        return $this->transformer->createMessageSeries($record);
    }

    public function findBySlug(Slug $slug): MessageSeries|null
    {
        $record = $this->findBySlug->find($slug);

        if ($record === null) {
            return null;
        }

        return $this->transformer->createMessageSeries($record);
    }

    public function deleteIds(UuidCollection $ids): Result
    {
        return $this->deleteIds->delete($ids);
    }
}
