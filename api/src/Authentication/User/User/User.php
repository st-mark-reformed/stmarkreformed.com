<?php

declare(strict_types=1);

namespace App\Authentication\User\User;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Spatie\Cloneable\Cloneable;

readonly class User
{
    use Cloneable;

    public bool $isValid;

    /** @var string[] */
    public array $errorMessages;

    public UuidInterface $id;

    public function __construct(
        public Email $email,
        public Roles $roles,
        public bool $isActive,
        UuidInterface|null $id = null,
    ) {
        if ($id === null) {
            $this->id = Uuid::uuid6();
        } else {
            $this->id = $id;
        }

        $isValid = true;

        $errorMessages = [];

        if (! $email->isValid) {
            $isValid         = false;
            $errorMessages[] = $email->errorMessage;
        }

        if (! $roles->isValid) {
            $isValid         = false;
            $errorMessages[] = $roles->errorMessage;
        }

        $this->isValid = $isValid;

        $this->errorMessages = $errorMessages;
    }

    public function withAddedRole(Role $role): User
    {
        return $this->with(roles: $this->roles->withAddedRole($role));
    }

    public function withIsActive(bool $isActive): User
    {
        return $this->with(isActive: $isActive);
    }

    public function withRemovedRole(Role $role): User
    {
        return $this->with(roles: $this->roles->withRemovedRole($role));
    }
}
