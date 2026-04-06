<?php

declare(strict_types=1);

namespace App\Messages\Search;

use Elastic\Elasticsearch\Client;

use function array_walk;
use function in_array;

readonly class DeleteMessagesFromIndexIfNotPresentInDb
{
    public function __construct(private Client $client)
    {
    }

    /**
     * @param string[] $messageIds
     * @param string[] $indexedIds
     */
    public function delete(array $messageIds, array $indexedIds): void
    {
        array_walk(
            $indexedIds,
            function (string $indexedId) use ($messageIds): void {
                if (
                    in_array(
                        $indexedId,
                        $messageIds,
                        true,
                    )
                ) {
                    return;
                }

                $this->client->delete([
                    'index' => MessagesSearchIndex::MESSAGES->value,
                    'id' => $indexedId,
                ]);
            },
        );
    }
}
