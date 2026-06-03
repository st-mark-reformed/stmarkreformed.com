<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Persistence\Create;

use App\EmptyUuid;
use App\MenOfTheMark\NewMenOfTheMarkItem;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;

use function array_keys;
use function assert;
use function implode;

readonly class MenOfTheMarkItemInserter
{
    public function __construct(
        private ApiPdo $pdo,
        private UuidFactoryInterface $uuidFactory,
    ) {
    }

    public function insert(NewMenOfTheMarkItem $menOfTheMarkItem): Result
    {
        if ($menOfTheMarkItem->id instanceof EmptyUuid) {
            /** @phpstan-ignore-next-line */
            $id = $this->uuidFactory->uuid7();
            assert($id instanceof UuidInterface);
        } else {
            $id = $menOfTheMarkItem->id;
        }

        $params = [
            'id' => $id->toString(),
            'enabled' => $menOfTheMarkItem->isEnabled ? '1' : '0',
            'date' => $menOfTheMarkItem->date->format('Y-m-d H:i:s'),
            'title' => $menOfTheMarkItem->title,
            'slug' => $menOfTheMarkItem->slug,
            'body' => $menOfTheMarkItem->body,
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'INSERT INTO men_of_the_mark (' . implode(', ', $columns) . ')',
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
