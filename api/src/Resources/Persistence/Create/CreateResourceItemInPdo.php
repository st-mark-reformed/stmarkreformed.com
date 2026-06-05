<?php

declare(strict_types=1);

namespace App\Resources\Persistence\Create;

use App\EmptyUuid;
use App\Persistence\ApiPdo;
use App\Resources\NewResourceItem;
use App\Result\Result;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;

use function array_keys;
use function assert;
use function implode;
use function json_encode;

readonly class CreateResourceItemInPdo
{
    public function __construct(
        private ApiPdo $pdo,
        private UuidFactoryInterface $uuidFactory,
    ) {
    }

    public function create(NewResourceItem $resourceItem): Result
    {
        if ($resourceItem->id instanceof EmptyUuid) {
            /** @phpstan-ignore-next-line */
            $id = $this->uuidFactory->uuid7();
            assert($id instanceof UuidInterface);
        } else {
            $id = $resourceItem->id;
        }

        $params = [
            'id' => $id->toString(),
            'enabled' => $resourceItem->isEnabled ? '1' : '0',
            'date' => $resourceItem->date->format('Y-m-d H:i:s'),
            'title' => $resourceItem->title,
            'slug' => $resourceItem->slug,
            'body' => $resourceItem->body,
            'downloads' => (string) json_encode(
                $resourceItem->downloads->asArray(),
            ),
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'INSERT INTO resources (' . implode(', ', $columns) . ')',
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
