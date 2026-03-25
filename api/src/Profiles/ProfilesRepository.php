<?php

declare(strict_types=1);

namespace App\Profiles;

use App\EmptyUuid;
use App\Profiles\Persistence\CreateProfile;
use App\Profiles\Persistence\DeleteProfile;
use App\Profiles\Persistence\FindAll;
use App\Profiles\Persistence\FindById;
use App\Profiles\Persistence\Transformer;
use App\Result\Result;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Throwable;

readonly class ProfilesRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private Transformer $transformer,
        private CreateProfile $createProfile,
        private DeleteProfile $deleteProfile,
    ) {
    }

    private function createUuid(string|UuidInterface $id): UuidInterface
    {
        if ($id instanceof UuidInterface) {
            return $id;
        }

        try {
            return Uuid::fromString($id);
        } catch (Throwable) {
            return new EmptyUuid();
        }
    }

    public function createProfile(NewProfile $newProfile): Result
    {
        return $this->createProfile->create(newProfile: $newProfile);
    }

    public function deleteProfile(string|UuidInterface $id): Result
    {
        return $this->deleteProfile->delete(id: $this->createUuid($id));
    }

    public function findAll(): Profiles
    {
        return $this->transformer->toEntities(records: $this->findAll->find());
    }

    public function findById(string|UuidInterface $id): ProfileResult
    {
        $record = $this->findById->find(id: $this->createUuid($id));

        if ($record === null) {
            return new ProfileResult(profile: null);
        }

        $profile = $this->transformer->toEntity($record);

        return new ProfileResult(profile: $profile);
    }
}
