<?php

declare(strict_types=1);

namespace App\News;

use App\News\Persistence\Create\CreateNewsItem;
use App\News\Persistence\Delete\DeleteNewsItem;
use App\News\Persistence\FindAll;
use App\News\Persistence\FindById;
use App\News\Persistence\Persist\PersistNewsItem;
use App\News\Persistence\Transformer;
use App\Persistence\CreateUuid;
use App\Result\Result;
use Ramsey\Uuid\UuidInterface;

readonly class NewsRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private CreateUuid $createUuid,
        private Transformer $transformer,
        private CreateNewsItem $createNewsItem,
        private DeleteNewsItem $deleteNewsItem,
        private PersistNewsItem $persistNewsItem,
    ) {
    }

    public function create(NewNewsItem $newsItem): Result
    {
        return $this->createNewsItem->create(newsItem: $newsItem);
    }

    public function delete(string|UuidInterface $id): Result
    {
        $newsItemResult = $this->findById(id: $id);

        if (! $newsItemResult->hasNewsItem) {
            return new Result();
        }

        return $this->deleteNewsItem->delete(newsItem: $newsItemResult->newsItem);
    }

    public function persist(NewsItem $newsItem): Result
    {
        return $this->persistNewsItem->persist(newsItem: $newsItem);
    }

    public function findAll(): NewsItems
    {
        return $this->transformer->toEntities(records: $this->findAll->find());
    }

    public function findById(string|UuidInterface $id): NewsItemResult
    {
        $record = $this->findById->find(
            id: $this->createUuid->fromStringOrInterface(id: $id),
        );

        if ($record === null) {
            return new NewsItemResult();
        }

        $newsItem = $this->transformer->toEntity(record: $record);

        return new NewsItemResult(newsItem: $newsItem);
    }
}
