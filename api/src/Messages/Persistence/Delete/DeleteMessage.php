<?php

declare(strict_types=1);

namespace App\Messages\Persistence\Delete;

use App\Messages\Message;
use App\Messages\Search\EnqueueIndexAllMessages;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class DeleteMessage
{
    public function __construct(
        private ApiPdo $pdo,
        private DeleteMessageAudioFile $deleteMessageAudioFile,
        private EnqueueIndexAllMessages $enqueueIndexAllMessages,
    ) {
    }

    public function delete(Message $message): Result
    {
        try {
            $this->pdo->beginTransaction();

            $statement = $this->pdo->prepare(
                'DELETE FROM messages WHERE id = :id',
            );

            $result = $statement->execute(
                ['id' => $message->id->toString()],
            );

            if (! $result) {
                return new Result(
                    success: false,
                    errors: ['An unexpected error occurred. Please try again later.'],
                );
            }

            $result = $this->deleteMessageAudioFile->delete($message);

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
}
