<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\EmptyUuid;
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

    public function create(NewProfile $profile): Result
    {
        if (! $profile->isValid) {
            return new Result(
                success: false,
                errors: $profile->validationMessages,
            );
        }

        if ($profile->id instanceof EmptyUuid) {
            /** @phpstan-ignore-next-line */
            $id = $this->uuidFactory->uuid7();
            assert($id instanceof UuidInterface);
        } else {
            $id = $profile->id;
        }

        $params = [
            'id' => $id->toString(),
            'title_or_honorific' => $profile->titleOrHonorific,
            'first_name' => $profile->firstName,
            'last_name' => $profile->lastName,
            'slug' => $profile->slug,
            'email' => $profile->email->toString(),
            'leadership_position' => $profile->leadershipPosition->value(),
            'bio' => $profile->bio,
            'has_messages' => $profile->hasMessages ? '1' : '0',
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
