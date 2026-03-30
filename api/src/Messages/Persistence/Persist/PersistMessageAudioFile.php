<?php

declare(strict_types=1);

namespace App\Messages\Persistence\Persist;

use App\Messages\Message;
use App\Result\Result;
use Throwable;

use function sprintf;

readonly class PersistMessageAudioFile
{
    public function __construct(
        private MessageAudioFileStorage $storage,
    ) {
    }

    public function persist(Message $message): Result
    {
        if (! $message->audioPathIsValidFileUpload()) {
            if ($message->audioPathIsFileUpload()) {
                return new Result(
                    success: false,
                    errors: ['audioPath' => 'Audio file is not a valid MP3.'],
                );
            }

            return new Result();
        }

        $absoluteFilePath = sprintf(
            '/var/www/public/uploads/audio/%s',
            $message->getAudioFileName(),
        );

        try {
            $this->storage->save(
                base64Audio: $message->audioPath,
                absoluteFilePath: $absoluteFilePath,
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
