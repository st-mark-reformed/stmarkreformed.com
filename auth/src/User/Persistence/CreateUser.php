<?php

declare(strict_types=1);

namespace App\User\Persistence;

use App\Persistence\AuthPdo;
use App\User\CreateUserResult;
use App\User\NewUser;
use App\User\UserRole;
use Exception;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;
use Throwable;

use function assert;
use function count;
use function implode;
use function password_hash;

use const PASSWORD_DEFAULT;

readonly class CreateUser
{
    public function __construct(
        private AuthPdo $pdo,
        private FindUserByEmail $findUserByEmail,
        private UuidFactoryInterface $uuidFactory,
    ) {
    }

    public function create(NewUser $newUser): CreateUserResult
    {
        $pre = $this->preValidate($newUser);

        if (! $pre->success) {
            return $pre;
        }

        try {
            $this->pdo->beginTransaction();

            /** @phpstan-ignore-next-line */
            $id = $this->uuidFactory->uuid7();
            assert($id instanceof UuidInterface);

            $statement = $this->pdo->prepare(implode(' ', [
                'INSERT INTO users (id, email, password_hash)',
                'VALUES (:id, :email, :password_hash)',
            ]));

            $result = $statement->execute([
                'id' => $id->toString(),
                'email' => $newUser->email->email,
                'password_hash' => $this->createPasswordHash($newUser),
            ]);

            if (! $result) {
                throw new Exception('Failed to insert user');
            }

            $this->insertRoles($id, $newUser);

            $this->pdo->commit();

            return new CreateUserResult();
        } catch (Throwable) {
            $this->pdo->rollBack();

            return new CreateUserResult(
                success: false,
                errors: ['An unexpected error occurred. Please try again later.'],
            );
        }
    }

    private function preValidate(NewUser $newUser): CreateUserResult
    {
        if (count($newUser->getValidationMessages()) > 0) {
            return new CreateUserResult(
                success: false,
                errors: $newUser->getValidationMessages(),
            );
        }

        $existingUser = $this->findUserByEmail->find(
            email: $newUser->email->email,
        );

        if ($existingUser->isValid()) {
            return new CreateUserResult(
                success: false,
                errors: ['That user already exists.'],
            );
        }

        return new CreateUserResult();
    }

    private function createPasswordHash(NewUser $newUser): string
    {
        if ($newUser->password === '') {
            return '';
        }

        return password_hash(
            $newUser->password,
            PASSWORD_DEFAULT,
        );
    }

    private function insertRoles(UuidInterface $id, NewUser $newUser): void
    {
        $newUser->roles->walk(
            function (UserRole $role) use ($id): void {
                $this->insertRole(id: $id, role: $role);
            },
        );
    }

    private function insertRole(UuidInterface $id, UserRole $role): void
    {
        $statement = $this->pdo->prepare(implode(' ', [
            'INSERT INTO user_roles (user_id, role)',
            'VALUES (:user_id, :role)',
        ]));

        $result = $statement->execute([
            'user_id' => $id,
            'role' => $role->name,
        ]);

        if ($result) {
            return;
        }

        throw new Exception('Failed to insert role');
    }
}
