<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use App\Messages\Message\Message;
use App\Persistence\PersistRecord;
use App\Persistence\Result;

readonly class PersistFactory
{
    public function __construct(
        private Transformer $transformer,
        private PersistRecord $persistRecord,
    ) {
    }

    public function persist(Message $message): Result
    {
        if (! $message->isValid) {
            return new Result(
                false,
                $message->errorMessages,
            );
        }

        // TODO: Validate unique slug

        $record = $this->transformer->createRecord($message);

        return $this->persistRecord->persist($record);
    }
}
