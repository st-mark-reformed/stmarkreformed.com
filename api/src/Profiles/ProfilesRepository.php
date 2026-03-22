<?php

declare(strict_types=1);

namespace App\Profiles;

use App\Profiles\Persistence\CreateProfile;
use App\Profiles\Persistence\DeleteProfile;
use App\Profiles\Persistence\FindAll;
use App\Profiles\Persistence\Transformer;
use App\Result;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

readonly class ProfilesRepository
{
    public function __construct(
        private FindAll $findAll,
        private Transformer $transformer,
        private CreateProfile $createProfile,
        private DeleteProfile $deleteProfile,
    ) {
    }

    public function createProfile(NewProfile $newProfile): Result
    {
        return $this->createProfile->create(newProfile: $newProfile);
    }

    public function deleteProfile(string|UuidInterface $id): Result
    {
        if (! $id instanceof UuidInterface) {
            $id = Uuid::fromString($id);
        }

        return $this->deleteProfile->delete(id: $id);
    }

    public function findAll(): Profiles
    {
        return $this->transformer->toEntities(records: $this->findAll->find());
    }
}
