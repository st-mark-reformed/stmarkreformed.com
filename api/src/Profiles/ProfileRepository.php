<?php

declare(strict_types=1);

namespace App\Profiles;

use App\Persistence\Result;
use App\Persistence\UuidCollection;
use App\Profiles\Persistence\CreateAndPersistFactory;
use App\Profiles\Persistence\DeleteIds;
use App\Profiles\Persistence\FindAll;
use App\Profiles\Persistence\FindById;
use App\Profiles\Persistence\FindByIds;
use App\Profiles\Persistence\PersistFactory;
use App\Profiles\Persistence\Transformer;
use App\Profiles\Profile\Profile;
use App\Profiles\Profile\Profiles;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

use function is_string;

readonly class ProfileRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private DeleteIds $deleteIds,
        private FindByIds $findByIds,
        private Transformer $transformer,
        private PersistFactory $persistFactory,
        private CreateAndPersistFactory $createAndPersistFactory,
    ) {
    }

    public function createAndPersist(Profile $profile): Result
    {
        return $this->createAndPersistFactory->create($profile);
    }

    public function persist(Profile $profile): Result
    {
        return $this->persistFactory->persist($profile);
    }

    public function findAll(): Profiles
    {
        return $this->transformer->createProfiles(
            $this->findAll->find(),
        );
    }

    /** @param string[] $ids */
    public function findByIds(array $ids): Profiles
    {
        return $this->transformer->createProfiles(
            $this->findByIds->find($ids),
        );
    }

    public function findById(UuidInterface|string $id): Profile|null
    {
        if (is_string($id)) {
            $id = Uuid::fromString($id);
        }

        $record = $this->findById->find($id);

        if ($record === null) {
            return null;
        }

        return $this->transformer->createProfile($record);
    }

    public function deleteIds(UuidCollection $ids): Result
    {
        return $this->deleteIds->delete($ids);
    }
}
