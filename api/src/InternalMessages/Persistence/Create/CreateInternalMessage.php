<?php

declare(strict_types=1);

namespace App\InternalMessages\Persistence\Create;

use App\InternalMessages\Generate\EnqueueGenerateInternalMediaPagesForRedis;
use App\InternalMessages\NewInternalMessage;
use App\InternalMessages\Persistence\Persist\PersistInternalMessageAudioFile;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class CreateInternalMessage
{
    public function __construct(
        private ApiPdo $pdo,
        private CreateInternalMessageInPdo $createInternalMessageInPdo,
        private EnqueueGenerateInternalMediaPagesForRedis $enqueueGenerateInternalMediaPagesForRedis,
        private PersistInternalMessageAudioFile $persistInternalMessageAudioFile,
    ) {
    }

    public function create(NewInternalMessage $message): Result
    {
        if (! $message->isValid) {
            return new Result(
                success: false,
                errors: $message->validationMessages,
            );
        }

        try {
            $this->pdo->beginTransaction();

            $result = $this->createInternalMessageInPdo->create(message: $message);

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
}
