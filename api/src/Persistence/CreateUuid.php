<?php

declare(strict_types=1);

namespace App\Persistence;

use App\EmptyUuid;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Throwable;

readonly class CreateUuid
{
    public function fromStringOrInterface(
        string|UuidInterface $id,
    ): UuidInterface {
        if ($id instanceof UuidInterface) {
            return $id;
        }

        try {
            return Uuid::fromString($id);
        } catch (Throwable) {
            return new EmptyUuid();
        }
    }
}
