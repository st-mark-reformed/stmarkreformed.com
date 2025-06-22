<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

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
    public function find(array $ids): ProfileRecords
    {
        $count = count($ids);

        if ($count < 1) {
            return new ProfileRecords();
        }

        $in = implode(
            ',',
            array_fill(
                0,
                $count,
                '?',
            ),
        );

        $columns = implode(', ', ProfileRecord::getColumns());

        $statement = $this->pdo->prepare(implode(' ', [
            'SELECT',
            $columns,
            'FROM',
            ProfileRecord::getTableName(),
            'WHERE id IN (' . $in . ')',
            'ORDER BY first_name, last_name ASC',
        ]));

        $statement->execute(array_values($ids));

        return new ProfileRecords($statement->fetchAll(
            PDO::FETCH_CLASS,
            ProfileRecord::class,
        ));
    }
}
