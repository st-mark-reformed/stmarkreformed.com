<?php

declare(strict_types=1);

namespace App\Messages;

use App\Messages\Message\Message;
use App\Messages\Message\Messages;
use App\Messages\Persistence\CreateAndPersistFactory;
use App\Messages\Persistence\FindAll;
use App\Messages\Persistence\Transformer;
use App\Persistence\Result;

readonly class MessageRepository
{
    public function __construct(
        private FindAll $findAll,
        private Transformer $transformer,
        private CreateAndPersistFactory $createAndPersistFactory,
    ) {
    }

    public function createAndPersist(Message $message): Result
    {
        return $this->createAndPersistFactory->create($message);
    }

    public function findAll(): Messages
    {
        return $this->transformer->createMessages(
            $this->findAll->find(),
        );
    }
}
