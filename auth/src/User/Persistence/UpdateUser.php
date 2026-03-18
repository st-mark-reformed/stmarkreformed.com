<?php

declare(strict_types=1);

namespace App\User\Persistence;

use App\Persistence\AuthPdo;
use App\User\Result;
use App\User\User;
use App\User\UserRole;
use Exception;
use Ramsey\Uuid\UuidInterface;
use Throwable;

use function array_filter;
use function array_find;
use function array_map;
use function count;
use function implode;

readonly class UpdateUser
{
    public function __construct(
        private AuthPdo $pdo,
        private InsertUserRole $insertUserRole,
        private FindUserByEmail $findUserByEmail,
    ) {
    }

    public function update(User $user): Result
    {
        $pre = $this->preValidate($user);

        if (! $pre->success) {
            return $pre;
        }

        try {
            $this->pdo->beginTransaction();

            $statement = $this->pdo->prepare(implode(
                ' ',
                [
                    'UPDATE users',
                    'SET email = :email, password_hash = :password_hash',
                    'WHERE id = :id',
                ],
            ));

            $result = $statement->execute([
                'id' => $user->id->toString(),
                'email' => $user->email->toString(),
                'password_hash' => $user->passwordHash,
            ]);

            if (! $result) {
                throw new Exception('Failed to update user');
            }

            $existingRecord = $this->findUserByEmail->find(
                email: $user->email->toString(),
            );

            $this->addUserRoles(
                existingUserRecord: $existingRecord,
                updatedUser: $user,
            );

            $this->removeUserRoles(
                existingUserRecord: $existingRecord,
                updatedUser: $user,
            );

            $this->pdo->commit();

            return new Result();
        } catch (Throwable) {
            $this->pdo->rollBack();

            return new Result(
                success: false,
                errors: ['An unexpected error occurred. Please try again later.'],
            );
        }
    }

    private function preValidate(User $user): Result
    {
        if (count($user->getValidationMessages()) > 0) {
            return new Result(
                success: false,
                errors: $user->getValidationMessages(),
            );
        }

        $existingUser = $this->findUserByEmail->find(
            email: $user->email->toString(),
            excludeIds: [$user->id->toString()],
        );

        if ($existingUser->isValid()) {
            return new Result(
                success: false,
                errors: ['User email conflicts with another user.'],
            );
        }

        return new Result();
    }

    private function addUserRoles(
        UserRecord $existingUserRecord,
        User $updatedUser,
    ): void {
        $neededNewRoles = $updatedUser->roles->filter(
            static function (UserRole $role) use ($existingUserRecord): bool {
                $existingRole = array_find(
                    $existingUserRecord->roles,
                    static fn (string $existing) => $existing === $role->name,
                );

                return $existingRole === null;
            },
        );

        $neededNewRoles->walk(
            function (UserRole $role) use ($updatedUser): void {
                $this->insertUserRole->insert(
                    id: $updatedUser->id,
                    role: $role,
                );
            },
        );
    }

    private function removeUserRoles(
        UserRecord $existingUserRecord,
        User $updatedUser,
    ): void {
        $neededRemovedRolesStrings = array_filter(
            $existingUserRecord->roles,
            static function (
                string $roleString,
            ) use ($updatedUser): bool {
                $isStillPresent = $updatedUser->roles->find(
                    static function (
                        UserRole $r,
                    ) use ($roleString): bool {
                        return $r->name === $roleString;
                    },
                );

                return $isStillPresent === null;
            },
        );

        array_map(
            function (string $role) use ($updatedUser): void {
                $this->removeUserRole(id: $updatedUser->id, role: $role);
            },
            $neededRemovedRolesStrings,
        );
    }

    private function removeUserRole(UuidInterface $id, string $role): void
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM user_roles WHERE user_id = :id AND role = :role',
        );

        $statement->execute([
            'id' => $id->toString(),
            'role' => $role,
        ]);
    }
}
