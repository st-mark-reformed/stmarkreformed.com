<?php

declare(strict_types=1);

namespace App\User;

use App\EmptyUuid;
use Ramsey\Uuid\UuidInterface;
use Spatie\Cloneable\Cloneable;

use function password_hash;
use function password_verify;

use const PASSWORD_DEFAULT;

readonly class User
{
    use Cloneable;

    public bool $isValid;

    public function __construct(
        public UuidInterface $id = new EmptyUuid(),
        public UserEmail $email = new UserEmail(),
        public string $passwordHash = '',
        public UserRoles $roles = new UserRoles(),
    ) {
        $this->isValid = ! $id instanceof EmptyUuid && $email->isValid;
    }

    /** @return string[] */
    public function getValidationMessages(): array
    {
        if ($this->email->isValid) {
            return [];
        }

        return ['A valid email address is required.'];
    }

    public function isPasswordValid(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    public function withAddedRole(UserRole $role): self
    {
        return $this->with(roles: $this->roles->withAddedRole(role: $role));
    }

    /** @param UserRole[] $roles */
    public function withAddedRoles(array $roles): self
    {
        return $this->with(roles: $this->roles->withAddedRoles(roles: $roles));
    }

    public function withRemovedRole(UserRole $role): self
    {
        return $this->with(roles: $this->roles->withRemovedRole(role: $role));
    }

    /** @param UserRole[] $roles */
    public function withRemovedRoles(array $roles): self
    {
        return $this->with(roles: $this->roles->withRemovedRoles(roles: $roles));
    }

    public function withNewPassword(string $password): self
    {
        return $this->with(passwordHash: password_hash(
            $password,
            PASSWORD_DEFAULT,
        ));
    }
}
