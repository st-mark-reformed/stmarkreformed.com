<?php

declare(strict_types=1);

namespace App\Messages\Series\Persistence;

use App\Messages\Series\MessageSeries\Slug;
use App\Persistence\ApiPdo;
use PDO;

use function assert;
use function implode;

readonly class FindBySlug
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(Slug $slug): MessageSeriesRecord|null
    {
        $columns = implode(
            ', ',
            MessageSeriesRecord::getColumns(),
        );

        $statement = $this->pdo->prepare(
            implode(' ', [
                'SELECT',
                $columns,
                'FROM',
                MessageSeriesRecord::getTableName(),
                'WHERE slug = :slug',
            ]),
        );

        $statement->execute(['slug' => $slug->slug]);

        $statement->setFetchMode(
            PDO::FETCH_CLASS,
            MessageSeriesRecord::class,
        );

        $record = $statement->fetch();

        assert($record instanceof MessageSeriesRecord || $record === false);

        return $record !== false ? $record : null;
    }
}
