<?php

declare(strict_types=1);

namespace App\Messages;

use App\Messages\Message\Message;
use App\Messages\Message\Messages;
use App\Messages\Persistence\CreateAndPersistFactory;
use App\Messages\Persistence\DeleteIds;
use App\Messages\Persistence\FindAll;
use App\Messages\Persistence\FindById;
use App\Messages\Persistence\PersistFactory;
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
        private Transformer $transformer,
        private PersistFactory $persistFactory,
        private CreateAndPersistFactory $createAndPersistFactory,
    ) {
    }

    public function createAndPersist(Message $message): Result
    {
        return $this->createAndPersistFactory->create($message);
    }

    public function persist(Message $message): Result
    {
        return $this->persistFactory->persist($message);
    }

    public function findAll(): Messages
    {
        return $this->transformer->createMessages(
            $this->findAll->find(),
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

    public function deleteIds(UuidCollection $ids): Result
    {
        return $this->deleteIds->delete($ids);
    }
}
