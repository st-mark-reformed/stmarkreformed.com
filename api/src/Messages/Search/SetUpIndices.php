<?php

declare(strict_types=1);

namespace App\Messages\Search;

use Elastic\Elasticsearch\Client;
use Throwable;

use function array_map;

readonly class SetUpIndices
{
    public function __construct(private Client $client)
    {
    }

    public function setUp(): void
    {
        array_map(
            function (MessagesSearchIndex $index): void {
                try {
                    $tmp = $this->client->indices()->get(
                        ['index' => $index->value],
                    );
                } catch (Throwable) {
                    $this->client->indices()->create(
                        ['index' => $index->value],
                    );
                }
            },
            MessagesSearchIndex::cases(),
        );
    }
}
