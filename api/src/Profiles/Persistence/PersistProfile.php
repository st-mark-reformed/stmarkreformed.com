<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Persistence\ApiPdo;
use App\Profiles\Profile;
use App\Result\Result;

use function array_keys;
use function array_map;
use function implode;

readonly class PersistProfile
{
    public function __construct(
        private ApiPdo $pdo,
        private FindById $findById,
    ) {
    }

    public function persist(Profile $profile): Result
    {
        if (! $profile->isValid) {
            return new Result(
                success: false,
                errors: $profile->validationMessages,
            );
        }

        $validIdResult = $this->idIsValid(profile: $profile);

        if (! $validIdResult->success) {
            return $validIdResult;
        }

        $params = [
            'title_or_honorific' => $profile->titleOrHonorific,
            'first_name' => $profile->firstName,
            'last_name' => $profile->lastName,
            'email' => $profile->email->toString(),
            'leadership_position' => $profile->leadershipPosition->name,
            'bio' => $profile->bio,
            'has_messages' => $profile->hasMessages ? '1' : '0',
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'UPDATE profiles',
            'SET',
            implode(', ', array_map(
                static fn (
                    string $column,
                ): string => $column . ' = :' . $column,
                $columns,
            )),
            'WHERE id = :id',
        ]));

        $params['id'] = $profile->id->toString();

        $result = $statement->execute($params);

        if (! $result) {
            return new Result(
                success: false,
                errors: ['An unexpected error occurred. Please try again later.'],
            );
        }

        return new Result();
    }

    public function idIsValid(Profile $profile): Result
    {
        $record = $this->findById->find(id: $profile->id);

        if ($record === null) {
            return new Result(
                success: false,
                errors: ['id' => 'Profile with this ID does not exist'],
            );
        }

        return new Result();
    }
}
