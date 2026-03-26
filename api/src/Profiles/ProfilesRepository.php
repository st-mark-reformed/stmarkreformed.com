<?php

declare(strict_types=1);

namespace App\Profiles;

use App\Persistence\CreateUuid;
use App\Profiles\Persistence\CreateProfile;
use App\Profiles\Persistence\DeleteProfile;
use App\Profiles\Persistence\FindAll;
use App\Profiles\Persistence\FindById;
use App\Profiles\Persistence\Transformer;
use App\Result\Result;
use Ramsey\Uuid\UuidInterface;

readonly class ProfilesRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private CreateUuid $createUuid,
        private Transformer $transformer,
        private CreateProfile $createProfile,
        private DeleteProfile $deleteProfile,
    ) {
    }

    public function create(NewProfile $newProfile): Result
    {
        return $this->createProfile->create(profile: $newProfile);
    }

    public function delete(string|UuidInterface $id): Result
    {
        return $this->deleteProfile->delete(
            id: $this->createUuid->fromStringOrInterface(id: $id),
        );
    }

    public function findAll(): Profiles
    {
        return $this->transformer->toEntities(records: $this->findAll->find());
    }

    public function findById(string|UuidInterface $id): ProfileResult
    {
        $record = $this->findById->find(
            id: $this->createUuid->fromStringOrInterface(id: $id),
        );

        if ($record === null) {
            return new ProfileResult(profile: null);
        }

        $profile = $this->transformer->toEntity($record);

        return new ProfileResult(profile: $profile);
    }
}
