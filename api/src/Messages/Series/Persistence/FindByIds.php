<?php

declare(strict_types=1);

namespace App\Messages\Series\Persistence;

use App\Persistence\ApiPdo;
use PDO;

use function array_fill;
use function array_values;
use function count;
use function implode;

readonly class FindByIds
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    /** @param string[] $ids */
    public function find(array $ids): MessageSeriesRecordCollection
    {
        $count = count($ids);

        if ($count < 1) {
            return new MessageSeriesRecordCollection();
        }

        $in = implode(
            ',',
            array_fill(
                0,
                $count,
                '?',
            ),
        );

        $columns = implode(
            ', ',
            MessageSeriesRecord::getColumns(),
        );

        $statement = $this->pdo->prepare(implode(' ', [
            'SELECT',
            $columns,
            'FROM',
            MessageSeriesRecord::getTableName(),
            'WHERE id IN (' . $in . ')',
            'ORDER BY title ASC',
        ]));

        $statement->execute(array_values($ids));

        return new MessageSeriesRecordCollection($statement->fetchAll(
            PDO::FETCH_CLASS,
            MessageSeriesRecord::class,
        ));
    }
}
