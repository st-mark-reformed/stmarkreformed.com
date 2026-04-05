<?php

declare(strict_types=1);

namespace App\Messages\Persistence\Create;

use App\EmptyUuid;
use App\Messages\NewMessage;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;

use function array_keys;
use function assert;
use function implode;

readonly class CreateMessageInPdo
{
    public function __construct(
        private ApiPdo $pdo,
        private UuidFactoryInterface $uuidFactory,
    ) {
    }

    public function create(NewMessage $message): Result
    {
        if ($message->id instanceof EmptyUuid) {
            /** @phpstan-ignore-next-line */
            $id = $this->uuidFactory->uuid7();
            assert($id instanceof UuidInterface);
        } else {
            $id = $message->id;
        }

        $params = [
            'id' => $id->toString(),
            'enabled' => $message->isEnabled ? '1' : '0',
            'date' => $message->date->format('Y-m-d H:i:s'),
            'title' => $message->title,
            'slug' => $message->slug,
            'audio_path' => $message->createAudioFileNameForPersistence(),
            'speaker_id' => $message->speakerId,
            'passage' => $message->passage,
            'series_id' => $message->seriesId,
            'description' => $message->description,
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'INSERT INTO messages (' . implode(', ', $columns) . ')',
            'VALUES (:' . implode(', :', $columns) . ')',
        ]));

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
