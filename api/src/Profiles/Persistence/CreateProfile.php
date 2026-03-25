<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Persistence\ApiPdo;
use App\Profiles\NewProfile;
use App\Result\Result;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;

use function array_keys;
use function assert;
use function implode;

readonly class CreateProfile
{
    public function __construct(
        private ApiPdo $pdo,
        private UuidFactoryInterface $uuidFactory,
    ) {
    }

    public function create(NewProfile $newProfile): Result
    {
        if (! $newProfile->isValid) {
            return new Result(
                success: false,
                errors: $newProfile->validationMessages,
            );
        }

        /** @phpstan-ignore-next-line */
        $id = $this->uuidFactory->uuid7();
        assert($id instanceof UuidInterface);

        $params = [
            'id' => $id->toString(),
            'title_or_honorific' => $newProfile->titleOrHonorific,
            'first_name' => $newProfile->firstName,
            'last_name' => $newProfile->lastName,
            'email' => $newProfile->email->toString(),
            'leadership_position' => $newProfile->leadershipPosition->value(),
            'bio' => $newProfile->bio,
            'has_messages' => $newProfile->hasMessages ? '1' : '0',
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'INSERT INTO profiles (' . implode(', ', $columns) . ')',
            'VALUES (:' . implode(', :', $columns) . ')',
        ]));

        $result = $statement->execute($params);

        if (! $result) {
            return new Result(
                success: false,
                errors: ['An unexpected error occurred. Please try again later.'],
            );
        }

        return new Result();
    }
}
