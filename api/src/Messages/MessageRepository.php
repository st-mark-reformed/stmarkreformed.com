<?php

declare(strict_types=1);

namespace App\Messages;

use App\Messages\Message\Message;
use App\Messages\Message\Messages;
use App\Messages\Message\Slug;
use App\Messages\Persistence\CreateAndPersistFactory;
use App\Messages\Persistence\DeleteIds;
use App\Messages\Persistence\FindAll;
use App\Messages\Persistence\FindAllByLimit;
use App\Messages\Persistence\FindAllBySpeakerByLimit;
use App\Messages\Persistence\FindAllInSeriesByLimit;
use App\Messages\Persistence\FindById;
use App\Messages\Persistence\FindBySlug;
use App\Messages\Persistence\Transformer;
use App\Persistence\Result;
use App\Persistence\UuidCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

use function implode;
use function is_string;

class MessageRepository
{
    public function __construct(
        private readonly FindAll $findAll,
        private readonly FindById $findById,
        private readonly DeleteIds $deleteIds,
        private readonly FindBySlug $findBySlug,
        private readonly Transformer $transformer,
        private readonly FindAllByLimit $findAllByLimit,
        private readonly FindAllInSeriesByLimit $findAllInSeriesByLimit,
        private readonly CreateAndPersistFactory $createAndPersistFactory,
        private readonly FindAllBySpeakerByLimit $findAllBySpeakerByLimit,
    ) {
    }

    public function createAndPersist(Message $message): Result
    {
        return $this->createAndPersistFactory->create($message);
    }

    public function persist(Message $message): Result
    {
        return $this->createAndPersistFactory->persist($message);
    }

    /** @var array<array-key, Messages> */
    private array $findAllMemo = [];

    public function findAll(
        PublishStatusOption $publishStatus = PublishStatusOption::ALL,
    ): Messages {
        $key = $publishStatus->name;

        if (! isset($this->findAllMemo[$key])) {
            $this->findAllMemo[$key] = $this->transformer->createMessages(
                $this->findAll->find(
                    $publishStatus,
                ),
            );
        }

        $aKey = PublishStatusOption::ALL->name;

        $pKey = PublishStatusOption::PUBLISHED->name;

        if (
            $publishStatus->name === $aKey
            && ! isset($this->findAllMemo[$pKey])
        ) {
            $this->findAllMemo[$pKey] = $this->findAllMemo[$key]->filter(
                static fn (Message $m) => $m->isPublished === true,
            );
        }

        $nKey = PublishStatusOption::NOT_PUBLISHED->name;

        if (
            $publishStatus->name === $aKey
            && ! isset($this->findAllMemo[$nKey])
        ) {
            $this->findAllMemo[$nKey] = $this->findAllMemo[$key]->filter(
                static fn (Message $m) => $m->isPublished !== true,
            );
        }

        return $this->findAllMemo[$key];
    }

    /** @var array<array-key, FindByLimitResults> */
    private array $findAllByLimitMemo = [];

    public function findAllByLimit(
        int $offset = 0,
        int $limit = 25,
        PublishStatusOption $publishStatus = PublishStatusOption::ALL,
    ): FindByLimitResults {
        $key = implode(
            '-',
            [
                $offset,
                $limit,
                $publishStatus->name,
            ],
        );

        $this->populateFindAllByLimitMemo(
            $key,
            $offset,
            $limit,
            $publishStatus,
        );

        return $this->findAllByLimitMemo[$key];
    }

    private function populateFindAllByLimitMemo(
        string $key,
        int $offset,
        int $limit,
        PublishStatusOption $publishStatus,
    ): void {
        if (isset($this->findAllByLimitMemo[$key])) {
            return;
        }

        $aKey = $publishStatus->name;

        if (isset($this->findAllMemo[$aKey])) {
            $allResults = $this->findAllMemo[$aKey];

            $this->findAllByLimitMemo[$key] = new FindByLimitResults(
                limit: $limit,
                offset: $offset,
                absoluteTotalResults: $allResults->count(),
                messages: $allResults->slice($offset, $limit),
            );

            return;
        }

        $recordResults = $this->findAllByLimit->find(
            offset: $offset,
            limit: $limit,
            publishStatus: $publishStatus,
        );

        $this->findAllByLimitMemo[$key] = new FindByLimitResults(
            limit: $recordResults->limit,
            offset: $recordResults->offset,
            absoluteTotalResults: $recordResults->absoluteTotalResults,
            messages: $this->transformer->createMessages(
                $recordResults->records,
            ),
        );
    }

    /** @var array<array-key, FindByLimitResults> */
    private array $findAllInSeriesByLimitMemo = [];

    public function findAllInSeriesByLimit(
        UuidInterface|string $seriesId,
        int $offset = 0,
        int $limit = 25,
        PublishStatusOption $publishStatus = PublishStatusOption::ALL,
    ): FindByLimitResults {
        if (is_string($seriesId)) {
            $seriesId = Uuid::fromString($seriesId);
        }

        $key = implode(
            '-',
            [
                $seriesId->toString(),
                $offset,
                $limit,
                $publishStatus->name,
            ],
        );

        $this->populateFindAllInSeriesByLimitMemo(
            $key,
            $seriesId,
            $offset,
            $limit,
            $publishStatus,
        );

        return $this->findAllInSeriesByLimitMemo[$key];
    }

    private function populateFindAllInSeriesByLimitMemo(
        string $key,
        UuidInterface $seriesId,
        int $offset,
        int $limit,
        PublishStatusOption $publishStatus,
    ): void {
        if (isset($this->findAllInSeriesByLimitMemo[$key])) {
            return;
        }

        $aKey = $publishStatus->name;

        if (isset($this->findAllMemo[$aKey])) {
            $allResults = $this->findAllMemo[$aKey];

            $filteredResults = $allResults->filter(
                static fn (
                    Message $m,
                ) => $m->series?->id->toString() === $seriesId->toString(),
            );

            $this->findAllInSeriesByLimitMemo[$key] = new FindByLimitResults(
                limit: $limit,
                offset: $offset,
                absoluteTotalResults: $filteredResults->count(),
                messages: $filteredResults->slice($offset, $limit),
            );

            return;
        }

        $recordResults = $this->findAllInSeriesByLimit->find(
            seriesId: $seriesId,
            offset: $offset,
            limit: $limit,
            publishStatus: $publishStatus,
        );

        $this->findAllInSeriesByLimitMemo[$key] = new FindByLimitResults(
            limit: $recordResults->limit,
            offset: $recordResults->offset,
            absoluteTotalResults: $recordResults->absoluteTotalResults,
            messages: $this->transformer->createMessages(
                $recordResults->records,
            ),
        );
    }

    /** @var array<array-key, FindByLimitResults> */
    private array $findAllBySpeakerByLimitMemo = [];

    public function findAllBySpeakerByLimit(
        UuidInterface|string $speakerId,
        int $offset = 0,
        int $limit = 25,
        PublishStatusOption $publishStatus = PublishStatusOption::ALL,
    ): FindByLimitResults {
        if (is_string($speakerId)) {
            $speakerId = Uuid::fromString($speakerId);
        }

        $key = implode(
            '-',
            [
                $speakerId->toString(),
                $offset,
                $limit,
                $publishStatus->name,
            ],
        );

        $this->populateFindAllBySpeakerByLimitMemo(
            $key,
            $speakerId,
            $offset,
            $limit,
            $publishStatus,
        );

        return $this->findAllBySpeakerByLimitMemo[$key];
    }

    private function populateFindAllBySpeakerByLimitMemo(
        string $key,
        UuidInterface $speakerId,
        int $offset,
        int $limit,
        PublishStatusOption $publishStatus,
    ): void {
        if (isset($this->findAllBySpeakerByLimitMemo[$key])) {
            return;
        }

        $aKey = $publishStatus->name;

        if (isset($this->findAllMemo[$aKey])) {
            $allResults = $this->findAllMemo[$aKey];

            $filteredResults = $allResults->filter(
                static fn (
                    Message $m,
                ) => $m->speaker?->id->toString() === $speakerId->toString(),
            );

            $this->findAllBySpeakerByLimitMemo[$key] = new FindByLimitResults(
                limit: $limit,
                offset: $offset,
                absoluteTotalResults: $filteredResults->count(),
                messages: $filteredResults->slice($offset, $limit),
            );

            return;
        }

        $recordResults = $this->findAllBySpeakerByLimit->find(
            speakerId: $speakerId,
            offset: $offset,
            limit: $limit,
            publishStatus: $publishStatus,
        );

        $this->findAllBySpeakerByLimitMemo[$key] = new FindByLimitResults(
            limit: $recordResults->limit,
            offset: $recordResults->offset,
            absoluteTotalResults: $recordResults->absoluteTotalResults,
            messages: $this->transformer->createMessages(
                $recordResults->records,
            ),
        );
    }

    /** @var array<array-key, Message|null> */
    public array $findByMemo = [];

    public function findById(UuidInterface|string $id): Message|null
    {
        if (is_string($id)) {
            $id = Uuid::fromString($id);
        }

        $key = $id->toString();

        if (! isset($this->findByMemo[$key])) {
            $record = $this->findById->find($id);

            if ($record === null) {
                $this->findByMemo[$key] = null;
            } else {
                $this->findByMemo[$key] = $this->transformer->createMessage(
                    $record,
                );
            }
        }

        return $this->findByMemo[$key];
    }

    public function findBySlug(
        Slug $slug,
        UuidInterface|string|null $excludeId = null,
    ): Message|null {
        if (is_string($excludeId)) {
            $excludeId = Uuid::fromString($excludeId);
        }

        $key = $slug->slug;

        if ($excludeId !== null) {
            $key .= '-' . $excludeId->toString();
        }

        if (! isset($this->findByMemo[$key])) {
            $record = $this->findBySlug->find(
                $slug,
                $excludeId,
            );

            if ($record === null) {
                $this->findByMemo[$key] = null;
            } else {
                $this->findByMemo[$key] = $this->transformer->createMessage(
                    $record,
                );
            }
        }

        return $this->findByMemo[$key];
    }

    public function deleteIds(UuidCollection $ids): Result
    {
        return $this->deleteIds->delete($ids);
    }
}
