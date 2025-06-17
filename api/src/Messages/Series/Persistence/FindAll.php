<?php

declare(strict_types=1);

namespace App\Messages\Series\Persistence;

use App\Persistence\ApiPdo;
use PDO;

use function implode;

readonly class FindAll
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(): MessageSeriesRecordCollection
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
                'ORDER BY title ASC',
            ]),
        );

        $statement->execute();

        return new MessageSeriesRecordCollection($statement->fetchAll(
            PDO::FETCH_CLASS,
            MessageSeriesRecord::class,
        ));
    }
}
