<?php

declare(strict_types=1);

namespace App\Persistence;

use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

use function array_map;
use function is_array;

readonly class UuidCollectionFactory
{
    public function fromServerRequest(
        ServerRequestInterface $request,
    ): UuidCollection {
        $submittedData = $request->getParsedBody();
        $submittedData = is_array($submittedData) ? $submittedData : [];

        $deleteIds = $submittedData['ids'] ?? [];
        $deleteIds = is_array($deleteIds) ? $deleteIds : [];

        return new UuidCollection(array_map(
            static fn (string $id) => Uuid::fromString($id),
            $deleteIds,
        ));
    }
}
