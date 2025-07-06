<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use App\Messages\PublishStatusOption;
use App\Persistence\ApiPdo;
use PDO;

use function array_filter;
use function implode;

readonly class FindAllByLimit
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(
        int $offset = 0,
        int $limit = 25,
        PublishStatusOption $publishStatus = PublishStatusOption::ALL,
    ): FindByLimitRecordResults {
        $columns = implode(', ', MessageRecord::getColumns());

        $sql = [
            'SELECT ' . $columns . ' FROM',
            MessageRecord::getTableName(),
        ];

        $sql[] = match ($publishStatus) {
            PublishStatusOption::PUBLISHED => 'WHERE is_published = 1',
            PublishStatusOption::NOT_PUBLISHED => 'WHERE is_published = 0',
            PublishStatusOption::ALL => '',
        };

        $sql = array_filter($sql, static fn ($str) => $str !== '');

        $countSql    = $sql;
        $countSql[0] = 'SELECT COUNT(*) FROM';

        $countStatement = $this->pdo->prepare(implode(
            ' ',
            $countSql,
        ));

        $countStatement->execute();

        $absoluteTotalResults = (int) $countStatement->fetchColumn();

        $sql[] = 'ORDER BY date DESC';
        $sql[] = 'LIMIT :limit';
        $sql[] = 'OFFSET :offset';

        $statement = $this->pdo->prepare(
            implode(' ', $sql),
        );

        $statement->execute([
            'limit' => $limit,
            'offset' => $offset,
        ]);

        return new FindByLimitRecordResults(
            limit: $limit,
            offset: $offset,
            absoluteTotalResults: $absoluteTotalResults,
            records: new MessageRecords($statement->fetchAll(
                PDO::FETCH_CLASS,
                MessageRecord::class,
            )),
        );
    }
}
