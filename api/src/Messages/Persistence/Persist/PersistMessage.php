<?php

declare(strict_types=1);

namespace App\Messages\Persistence\Persist;

use App\Messages\Message;
use App\Messages\Persistence\FindById;
use App\Messages\Search\EnqueueIndexAllMessages;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class PersistMessage
{
    public function __construct(
        private ApiPdo $pdo,
        private FindById $findById,
        private PersistMessageToPdo $persistMessageToPdo,
        private EnqueueIndexAllMessages $enqueueIndexAllMessages,
        private PersistMessageAudioFile $persistMessageAudioFile,
    ) {
    }

    public function persist(Message $message): Result
    {
        if (! $message->isValid) {
            return new Result(
                success: false,
                errors: $message->validationMessages,
            );
        }

        $validIdResult = $this->idIsValid(message: $message);

        if (! $validIdResult->success) {
            return $validIdResult;
        }

        try {
            $this->pdo->beginTransaction();

            $result = $this->persistMessageToPdo->persist(message: $message);

            if (! $result->success) {
                throw $result;
            }

            $result = $this->persistMessageAudioFile->persist(
                message: $message,
            );

            if (! $result->success) {
                throw $result;
            }

            $this->pdo->commit();

            $this->enqueueIndexAllMessages->enqueue();

            return new Result();
        } catch (Throwable $error) {
            $this->pdo->rollBack();

            if ($error instanceof Result) {
                return $error;
            }

            return new Result(
                success: false,
                errors: ['An unexpected error occurred. Please try again later.'],
            );
        }
    }

    private function idIsValid(Message $message): Result
    {
        $record = $this->findById->find(id: $message->id);

        if ($record === null) {
            return new Result(
                success: false,
                errors: ['id' => 'Message with this ID does not exist'],
            );
        }

        return new Result();
    }
}
