<?php

declare(strict_types=1);

namespace App\Persistence;

use RuntimeException;

use function array_filter;
use function array_keys;
use function array_merge;
use function get_object_vars;
use function implode;
use function in_array;
use function is_bool;

use const ARRAY_FILTER_USE_KEY;

abstract class Record
{
    protected const string UNIQUE_IDENTIFIER = 'id';

    abstract public static function getTableName(): string;

    abstract public function tableName(): string;

    /**
     * Ensure all columns are explicitly declared on the record. If we change
     * a column name, we'll get an exception when PDO tries to populate this
     */
    public function __set(string $name, mixed $value): void
    {
        throw new RuntimeException(
            'Property ' . $name . ' must be declared explicitly',
        );
    }

    /** @return array<string, mixed> */
    public function asArray(): array
    {
        return get_object_vars($this);
    }

    /** @return array<string, mixed> */
    public function asParametersArray(): array
    {
        $array = $this->asArray();

        foreach ($array as $key => $value) {
            if (! is_bool($value)) {
                continue;
            }

            $array[$key] = $value ? '1' : '0';
        }

        return $array;
    }

    /** @return array<string, string> */
    public static function getColumns(): array
    {
        /** @phpstan-ignore-next-line */
        $placeholder = new static();

        return $placeholder->columns();
    }

    /** @return array<string, string> */
    public function columns(string $prefix = ''): array
    {
        $properties = get_object_vars($this);

        $columns = [];

        foreach (array_keys($properties) as $property) {
            $columns[$property] = $prefix . $property;
        }

        return $columns;
    }

    public function columnsAsInsertIntoString(): string
    {
        return '(' . implode(', ', $this->columns()) . ')';
    }

    public function columnsAsValuePlaceholders(): string
    {
        return '(' .
            implode(', ', $this->columns(':')) .
            ')';
    }

    /** @param array<string> $exclude */
    public function columnsAsUpdateSetPlaceholders(array $exclude = []): string
    {
        $value          = [];
        $defaultExclude = [self::UNIQUE_IDENTIFIER];

        foreach ($this->columns() as $column) {
            if (in_array($column, array_merge($exclude, $defaultExclude), true)) {
                continue;
            }

            $value[] = $column . ' = :' . $column;
        }

        return implode(', ', $value);
    }

    /**
     * @param array<string> $exclude
     *
     * @return array<string, mixed>
     */
    public function asUpdateParametersArray(array $exclude = []): array
    {
        $params = $this->asParametersArray();

        if ($exclude === []) {
            return $params;
        }

        return array_filter(
            $params,
            static fn ($param) => ! in_array($param, $exclude, true),
            ARRAY_FILTER_USE_KEY,
        );
    }
}
