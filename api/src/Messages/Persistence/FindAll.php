<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use App\Messages\PublishStatusOption;
use App\Persistence\ApiPdo;
use PDO;

use function array_filter;
use function implode;

readonly class FindAll
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(
        PublishStatusOption $publishStatus = PublishStatusOption::ALL,
    ): MessageRecords {
        $columns = implode(', ', MessageRecord::getColumns());

        $sql = [
            'SELECT',
            $columns,
            'FROM',
            MessageRecord::getTableName(),
        ];

        $sql[] = match ($publishStatus) {
            PublishStatusOption::PUBLISHED => 'WHERE is_published = 1',
            PublishStatusOption::NOT_PUBLISHED => 'WHERE is_published = 0',
            PublishStatusOption::ALL => '',
        };

        $sql[] = 'ORDER BY date DESC';

        $statement = $this->pdo->prepare(
            implode(' ', array_filter(
                $sql,
                static fn ($str) => $str !== '',
            )),
        );

        $statement->execute();

        return new MessageRecords($statement->fetchAll(
            PDO::FETCH_CLASS,
            MessageRecord::class,
        ));
    }
}
