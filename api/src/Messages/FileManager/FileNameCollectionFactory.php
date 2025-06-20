<?php

declare(strict_types=1);

namespace App\Messages\FileManager;

use Psr\Http\Message\ServerRequestInterface;

use function is_array;

readonly class FileNameCollectionFactory
{
    public function fromServerRequest(
        ServerRequestInterface $request,
    ): FileNameCollection {
        $submittedData = $request->getParsedBody();
        $submittedData = is_array($submittedData) ? $submittedData : [];

        $deleteNames = $submittedData['names'] ?? [];
        $deleteNames = is_array($deleteNames) ? $deleteNames : [];

        return new FileNameCollection($deleteNames);
    }
}
