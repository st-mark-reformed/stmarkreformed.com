<?php

declare(strict_types=1);

namespace App\News\Persistence\Create;

use App\EmptyUuid;
use App\News\NewNewsItem;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;

use function array_keys;
use function assert;
use function implode;

readonly class CreateNewsItemInPdo
{
    public function __construct(
        private ApiPdo $pdo,
        private UuidFactoryInterface $uuidFactory,
    ) {
    }

    public function create(NewNewsItem $newsItem): Result
    {
        if ($newsItem->id instanceof EmptyUuid) {
            /** @phpstan-ignore-next-line */
            $id = $this->uuidFactory->uuid7();
            assert($id instanceof UuidInterface);
        } else {
            $id = $newsItem->id;
        }

        $params = [
            'id' => $id->toString(),
            'enabled' => $newsItem->isEnabled ? '1' : '0',
            'date' => $newsItem->date->format('Y-m-d H:i:s'),
            'title' => $newsItem->title,
            'slug' => $newsItem->slug,
            'heading' => $newsItem->heading,
            'subheading' => $newsItem->subheading,
            'body' => $newsItem->body,
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'INSERT INTO news (' . implode(', ', $columns) . ')',
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
