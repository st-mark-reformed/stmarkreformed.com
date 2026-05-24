<?php

declare(strict_types=1);

namespace App\Messages;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

use function file_exists;
use function filesize;
use function sprintf;

readonly class BackfillAudioFileSizesCommand
{
    private const string AUDIO_DIRECTORY = '/var/www/public/uploads/audio';

    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'messages:backfill-audio-file-sizes',
            self::class,
        );
    }

    public function __construct(private MessagesRepository $repository)
    {
    }

    public function __invoke(): int
    {
        $this->repository->findAll()->map(
            callback: function (Message $message): void {
                $size = $this->resolveSize(audioPath: $message->audioPath);

                if ($size === $message->audioFileSize) {
                    return;
                }

                $this->repository->persist(
                    message: $message->withAudioFileSize(value: $size),
                );
            },
        );

        return 0;
    }

    private function resolveSize(string $audioPath): int
    {
        if ($audioPath === '') {
            return 0;
        }

        $absolutePath = sprintf(
            '%s/%s',
            self::AUDIO_DIRECTORY,
            $audioPath,
        );

        if (! file_exists($absolutePath)) {
            return 0;
        }

        $size = filesize($absolutePath);

        if ($size === false) {
            return 0;
        }

        return $size;
    }
}
