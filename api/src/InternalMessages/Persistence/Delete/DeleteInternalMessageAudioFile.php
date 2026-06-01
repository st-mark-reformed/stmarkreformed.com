<?php

declare(strict_types=1);

namespace App\InternalMessages\Persistence\Delete;

use App\InternalMessages\InternalMessage;
use App\InternalMessages\Persistence\Persist\InternalMessageAudioFileStorage;
use App\Result\Result;
use Throwable;

readonly class DeleteInternalMessageAudioFile
{
    public function __construct(private InternalMessageAudioFileStorage $storage)
    {
    }

    public function delete(InternalMessage $message): Result
    {
        try {
            $this->storage->delete(
                slug: $message->slug,
                fileName: $message->audioPath,
            );
        } catch (Throwable $error) {
            return new Result(
                success: false,
                errors: ['audioPath' => $error->getMessage()],
            );
        }

        return new Result();
    }
}
