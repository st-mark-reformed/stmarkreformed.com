<?php

declare(strict_types=1);

namespace App\Messages\Persistence\Delete;

use App\Messages\Message;
use App\Messages\Persistence\Persist\MessageAudioFileStorage;
use App\Result\Result;
use Throwable;

use function sprintf;

readonly class DeleteMessageAudioFile
{
    public function __construct(private MessageAudioFileStorage $storage)
    {
    }

    public function delete(Message $message): Result
    {
        $absoluteFilePath = sprintf(
            '/var/www/public/uploads/audio/%s',
            $message->getAudioFileName(),
        );

        try {
            $this->storage->delete(absoluteFilePath: $absoluteFilePath);
        } catch (Throwable $error) {
            return new Result(
                success: false,
                errors: ['audioPath' => $error->getMessage()],
            );
        }

        return new Result();
    }
}
