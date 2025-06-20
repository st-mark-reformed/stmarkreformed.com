<?php

declare(strict_types=1);

namespace App\Messages\FileManager;

use SplFileInfo;

use function array_map;
use function array_values;

readonly class Files
{
    /** @var SplFileInfo[] $files */
    public array $files;

    /** @param SplFileInfo[] $files */
    public function __construct(array $files = [])
    {
        $this->files = array_values(array_map(
            static fn (SplFileInfo $i) => $i,
            $files,
        ));
    }

    /** @return Array<array-key, Array<array-key, scalar>> */
    public function asScalar(): array
    {
        return array_map(
            static fn (SplFileInfo $i) => [
                'path' => $i->getPathname(),
                'filename' => $i->getFilename(),
                'basename' => $i->getBasename(
                    '.' . $i->getExtension(),
                ),
                'extension' => $i->getExtension(),
                'directory' => $i->getPath(),
                'size' => $i->getSize(),
            ],
            $this->files,
        );
    }
}
