<?php

declare(strict_types=1);

namespace App\Messages\FileManager\Internal;

use App\Messages\FileManager\FileNameCollection;
use App\Persistence\Result;
use Symfony\Component\Filesystem\Filesystem;

use function in_array;

readonly class DeleteNames
{
    public function __construct(
        private FileFinder $fileFinder,
        private Filesystem $filesystem,
    ) {
    }

    public function delete(FileNameCollection $fileNames): Result
    {
        if (! $fileNames->hasNames()) {
            return new Result(true, []);
        }

        $filesExist = $fileNames->map(
            fn (string $fileName) => $this->fileFinder->fileExists(
                $fileName,
            ),
        );

        if (in_array(false, $filesExist, true)) {
            return new Result(
                false,
                ['Specified file(s) do not exist'],
            );
        }

        $this->filesystem->remove($fileNames->map(
            fn (string $name) => $this->fileFinder->createFilePath(
                $name,
            ),
        ));

        return new Result(true, ['Files were deleted']);
    }
}
