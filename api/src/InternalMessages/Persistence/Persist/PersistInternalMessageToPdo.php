<?php

declare(strict_types=1);

namespace App\InternalMessages\Persistence\Persist;

use App\InternalMessages\InternalMessage;
use App\Persistence\ApiPdo;
use App\Result\Result;

use function array_keys;
use function array_map;
use function implode;

readonly class PersistInternalMessageToPdo
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function persist(InternalMessage $message): Result
    {
        $params = [
            'enabled' => $message->isEnabled ? '1' : '0',
            'date' => $message->date->format('Y-m-d H:i:s'),
            'title' => $message->title,
            'slug' => $message->slug,
            'audio_path' => $message->createAudioFileNameForPersistence(),
            'audio_file_size' => $message->computeAudioFileSize(),
            'speaker_id' => $message->speaker->id->toString(),
            'passage' => $message->passage,
            'series_id' => $message->series->id->toString(),
            'description' => $message->description,
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'UPDATE internal_messages',
            'SET',
            implode(', ', array_map(
                static fn (
                    string $column,
                ): string => $column . ' = :' . $column,
                $columns,
            )),
            'WHERE id = :id',
        ]));

        $params['id'] = $message->id->toString();

        $result = $statement->execute($params);

        if (! $result) {
            return new Result(
                success: false,
                errors: ['An unexpected error occurred. Please try again later.'],
            );
        }

        return new Result();
    }
}
