<?php

declare(strict_types=1);

namespace App\Messages\FileManager\Internal;

use App\Messages\FileManager\FileRepository;
use App\Messages\FileManager\Files;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

use function array_map;
use function array_reverse;
use function iterator_to_array;

readonly class FindAllFiles
{
    public function find(): Files
    {
        $finder = (new Finder())->files()->in(
            FileRepository::BASE_PATH,
        )->sortByModifiedTime();

        $files = array_map(
            static fn (SplFileInfo $file) => $file,
            array_reverse(iterator_to_array($finder)),
        );

        return new Files($files);
    }
}
