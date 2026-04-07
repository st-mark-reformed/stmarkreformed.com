<?php

declare(strict_types=1);

namespace App\Messages\Persistence\Create;

use App\Messages\NewMessage;
use App\Messages\Persistence\Persist\PersistMessageAudioFile;
use App\Messages\Search\EnqueueIndexAllMessages;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class CreateMessage
{
    public function __construct(
        private ApiPdo $pdo,
        private CreateMessageInPdo $createMessageInPdo,
        private EnqueueIndexAllMessages $enqueueIndexAllMessages,
        private PersistMessageAudioFile $persistMessageAudioFile,
    ) {
    }

    public function create(NewMessage $message): Result
    {
        if (! $message->isValid) {
            return new Result(
                success: false,
                errors: $message->validationMessages,
            );
        }

        try {
            $this->pdo->beginTransaction();

            $result = $this->createMessageInPdo->create(message: $message);

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
}
