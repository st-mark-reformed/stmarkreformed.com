<?php

declare(strict_types=1);

namespace App\InternalMessages\Persistence\Persist;

use App\InternalMessages\Generate\EnqueueGenerateInternalMediaPagesForRedis;
use App\InternalMessages\InternalMessage;
use App\InternalMessages\Persistence\FindById;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class PersistInternalMessage
{
    public function __construct(
        private ApiPdo $pdo,
        private FindById $findById,
        private PersistInternalMessageToPdo $persistInternalMessageToPdo,
        private EnqueueGenerateInternalMediaPagesForRedis $enqueueGenerateInternalMediaPagesForRedis,
        private PersistInternalMessageAudioFile $persistInternalMessageAudioFile,
    ) {
    }

    public function persist(InternalMessage $message): Result
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

            $result = $this->persistInternalMessageToPdo->persist(message: $message);

            if (! $result->success) {
                throw $result;
            }

            $result = $this->persistInternalMessageAudioFile->persist(
                message: $message,
            );

            if (! $result->success) {
                throw $result;
            }

            $this->pdo->commit();

            $this->enqueueGenerateInternalMediaPagesForRedis->enqueue();

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

    private function idIsValid(InternalMessage $message): Result
    {
        $record = $this->findById->find(id: $message->id);

        if ($record === null) {
            return new Result(
                success: false,
                errors: ['id' => 'Internal message with this ID does not exist'],
            );
        }

        return new Result();
    }
}
