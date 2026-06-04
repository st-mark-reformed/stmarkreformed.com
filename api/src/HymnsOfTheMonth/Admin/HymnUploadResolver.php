<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Admin;

use App\HymnsOfTheMonth\HymnPracticeTrack;
use App\HymnsOfTheMonth\HymnPracticeTracks;
use App\HymnsOfTheMonth\Persistence\Persist\HymnFileStorage;
use RxAnte\AppBootstrap\Request\ServerRequest;

use function is_array;
use function is_string;

/**
 * Turns the admin create/edit request into resolved, on-disk file paths.
 *
 * Each upload field is either a new file (base64 `fileData` + `fileName`, which
 * gets written to disk) or an already-stored relative `path` that is kept as-is.
 * Files are written here, in the admin layer, so the domain entity only ever
 * holds real relative paths — the same shape the importer and Redis generator
 * rely on. A failed DB write afterward could orphan a freshly written file under
 * the hymn's own {slug} folder; it is harmless and overwritten on the next save.
 */
readonly class HymnUploadResolver
{
    public function __construct(private HymnFileStorage $storage)
    {
    }

    public function resolveMusicSheetPath(
        string $slug,
        ServerRequest $request,
    ): string {
        $raw = $request->parsedBody->attributes['musicSheet'] ?? null;

        if (! is_array($raw)) {
            return '';
        }

        $fileData = $this->stringValue(value: $raw['fileData'] ?? null);

        if ($fileData !== '') {
            return $this->storage->saveSheet(
                base64: $fileData,
                slug: $slug,
                fileName: $this->stringValue(value: $raw['fileName'] ?? null),
            );
        }

        return $this->stringValue(value: $raw['path'] ?? null);
    }

    public function resolvePracticeTracks(
        string $slug,
        ServerRequest $request,
    ): HymnPracticeTracks {
        $raw = $request->parsedBody->attributes['practiceTracks'] ?? null;

        if (! is_array($raw)) {
            return new HymnPracticeTracks();
        }

        $tracks = [];

        foreach ($raw as $rawTrack) {
            if (! is_array($rawTrack)) {
                continue;
            }

            $track = $this->resolveTrack(slug: $slug, rawTrack: $rawTrack);

            if ($track === null) {
                continue;
            }

            $tracks[] = $track;
        }

        return new HymnPracticeTracks(tracks: $tracks);
    }

    /** @param array<array-key, mixed> $rawTrack */
    private function resolveTrack(
        string $slug,
        array $rawTrack,
    ): HymnPracticeTrack|null {
        $title    = $this->stringValue(value: $rawTrack['title'] ?? null);
        $fileData = $this->stringValue(value: $rawTrack['fileData'] ?? null);

        $path = $fileData !== ''
            ? $this->storage->saveTrack(
                base64: $fileData,
                slug: $slug,
                fileName: $this->stringValue(value: $rawTrack['fileName'] ?? null),
            )
            : $this->stringValue(value: $rawTrack['path'] ?? null);

        if ($title === '' && $path === '') {
            return null;
        }

        return new HymnPracticeTrack(title: $title, path: $path);
    }

    private function stringValue(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }
}
