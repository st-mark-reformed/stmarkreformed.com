<?php

declare(strict_types=1);

namespace App\ElasticSearch\Index\Messages\All;

use Elasticsearch\Client;

use function array_walk;
use function in_array;

class DeleteEntriesNotPresent
{
    public function __construct(private Client $client)
    {
    }

    /**
     * @param string[] $entryIds
     * @param string[] $indexedIds
     */
    public function run(array $entryIds, array $indexedIds): void
    {
        array_walk(
            $indexedIds,
            function (string $indexedId) use ($entryIds): void {
                if (
                    in_array(
                        $indexedId,
                        $entryIds,
                        true
                    )
                ) {
                    return;
                }

                $this->client->delete([
                    'index' => 'messages',
                    'id' => $indexedId,
                ]);
            }
        );
    }
}
