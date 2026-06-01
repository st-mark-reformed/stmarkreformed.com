<?php

declare(strict_types=1);

namespace App\InternalMessages\Persistence\Delete;

use App\InternalMessages\Generate\EnqueueGenerateInternalMediaPagesForRedis;
use App\InternalMessages\InternalMessage;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class DeleteInternalMessage
{
    public function __construct(
        private ApiPdo $pdo,
        private DeleteInternalMessageAudioFile $deleteInternalMessageAudioFile,
        private EnqueueGenerateInternalMediaPagesForRedis $enqueueGenerateInternalMediaPagesForRedis,
    ) {
    }

    public function delete(InternalMessage $message): Result
    {
        try {
            $this->pdo->beginTransaction();

            $statement = $this->pdo->prepare(
                'DELETE FROM internal_messages WHERE id = :id',
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

            $result = $this->deleteInternalMessageAudioFile->delete($message);

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
