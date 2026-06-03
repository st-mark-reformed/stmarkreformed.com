<?php

declare(strict_types=1);

namespace App\PastorsPage\Persistence\Create;

use App\EmptyUuid;
use App\PastorsPage\NewPastorsPageItem;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;

use function array_keys;
use function assert;
use function implode;

readonly class CreatePastorsPageItemInPdo
{
    public function __construct(
        private ApiPdo $pdo,
        private UuidFactoryInterface $uuidFactory,
    ) {
    }

    public function create(NewPastorsPageItem $pastorsPageItem): Result
    {
        if ($pastorsPageItem->id instanceof EmptyUuid) {
            /** @phpstan-ignore-next-line */
            $id = $this->uuidFactory->uuid7();
            assert($id instanceof UuidInterface);
        } else {
            $id = $pastorsPageItem->id;
        }

        $params = [
            'id' => $id->toString(),
            'enabled' => $pastorsPageItem->isEnabled ? '1' : '0',
            'date' => $pastorsPageItem->date->format('Y-m-d H:i:s'),
            'title' => $pastorsPageItem->title,
            'slug' => $pastorsPageItem->slug,
            'heading' => $pastorsPageItem->heading,
            'subheading' => $pastorsPageItem->subheading,
            'body' => $pastorsPageItem->body,
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'INSERT INTO pastors_page (' . implode(', ', $columns) . ')',
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
