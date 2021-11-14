<?php

declare(strict_types=1);

namespace App\Messages\RetrieveMessages;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Entry;

use function count;

class MessageRetrieval
{
    public function __construct(
        private EntryQueryFactory $queryFactory,
        private ElasticUidRetrieval $elasticUidRetrieval,
    ) {
    }

    public function fromParams(
        ?MessageRetrievalParams $params = null,
    ): MessagesResult {
        $params ??= new MessageRetrievalParams();

        $searchUids = $this->elasticUidRetrieval->fromParams(params: $params);

        $query = $this->queryFactory->make();

        $query->section('messages');

        if (count($searchUids) > 0) {
            $query->uid($searchUids);
        }

        $start = $params->dateRangeStart();

        $end = $params->dateRangeEnd();

        if ($start !== null && $end !== null) {
            $query->postDate([
                'and',
                '>= ' . $start->format('Y-m-d'),
                '<= ' . $end->format('Y-m-d'),
            ]);
        } elseif ($start !== null) {
            $query->postDate('>= ' . $start->format('Y-m-d'));
        } elseif ($end !== null) {
            $query->postDate('<= ' . $end->format('Y-m-d'));
        }

        $absoluteTotal = (int) $query->count();

        $query->limit($params->limit());

        $query->offset($params->offset());

        /** @var Entry[] $entries */
        $entries = $query->all();

        return new MessagesResult(
            absoluteTotal: $absoluteTotal,
            messages: $entries,
        );
    }
}
