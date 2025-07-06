<?php

declare(strict_types=1);

namespace App\Messages;

use App\Messages\Message\Message;
use App\Messages\Message\Messages;
use App\Messages\Message\Slug;
use App\Messages\Persistence\CreateAndPersistFactory;
use App\Messages\Persistence\DeleteIds;
use App\Messages\Persistence\FindAll;
use App\Messages\Persistence\FindAllByLimit;
use App\Messages\Persistence\FindById;
use App\Messages\Persistence\FindBySlug;
use App\Messages\Persistence\Transformer;
use App\Persistence\Result;
use App\Persistence\UuidCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

use function is_string;

readonly class MessageRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private DeleteIds $deleteIds,
        private FindBySlug $findBySlug,
        private Transformer $transformer,
        private FindAllByLimit $findAllByLimit,
        private CreateAndPersistFactory $createAndPersistFactory,
    ) {
    }

    public function createAndPersist(Message $message): Result
    {
        return $this->createAndPersistFactory->create($message);
    }

    public function persist(Message $message): Result
    {
        return $this->createAndPersistFactory->persist($message);
    }

    public function findAll(
        PublishStatusOption $publishStatus = PublishStatusOption::ALL,
    ): Messages {
        return $this->transformer->createMessages(
            $this->findAll->find($publishStatus),
        );
    }

    public function findAllByLimit(
        int $offset = 0,
        int $limit = 25,
        PublishStatusOption $publishStatus = PublishStatusOption::ALL,
    ): FindByLimitResults {
        $recordResults = $this->findAllByLimit->find(
            offset: $offset,
            limit: $limit,
            publishStatus: $publishStatus,
        );

        return new FindByLimitResults(
            $recordResults->limit,
            $recordResults->offset,
            $recordResults->absoluteTotalResults,
            $this->transformer->createMessages(
                $recordResults->records,
            ),
        );
    }

    public function findById(UuidInterface|string $id): Message|null
    {
        if (is_string($id)) {
            $id = Uuid::fromString($id);
        }

        $record = $this->findById->find($id);

        if ($record === null) {
            return null;
        }

        return $this->transformer->createMessage($record);
    }

    public function findBySlug(
        Slug $slug,
        UuidInterface|string|null $excludeId = null,
    ): Message|null {
        if (is_string($excludeId)) {
            $excludeId = Uuid::fromString($excludeId);
        }

        $record = $this->findBySlug->find($slug, $excludeId);

        if ($record === null) {
            return null;
        }

        return $this->transformer->createMessage($record);
    }

    public function deleteIds(UuidCollection $ids): Result
    {
        return $this->deleteIds->delete($ids);
    }
}
