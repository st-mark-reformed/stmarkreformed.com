<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use App\Messages\Message;
use App\Persistence\ApiPdo;
use App\Result\Result;

use function array_keys;
use function array_map;
use function implode;

readonly class PersistMessage
{
    public function __construct(
        private ApiPdo $pdo,
        private FindById $findById,
    ) {
    }

    public function persist(Message $message): Result
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

        $params = [
            'enabled' => $message->isEnabled ? '1' : '0',
            'date' => $message->date->format('Y-m-d H:i:s'),
            'title' => $message->title,
            'slug' => $message->slug,
            'audio_path' => $message->audioPath,
            'speaker_id' => $message->speaker->id->toString(),
            'passage' => $message->passage,
            'series_id' => $message->speaker->id->toString(),
            'description' => $message->description,
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'UPDATE messages',
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

    private function idIsValid(Message $message): Result
    {
        $record = $this->findById->find(id: $message->id);

        if ($record === null) {
            return new Result(
                success: false,
                errors: ['id' => 'Message with this ID does not exist'],
            );
        }

        return new Result();
    }
}
