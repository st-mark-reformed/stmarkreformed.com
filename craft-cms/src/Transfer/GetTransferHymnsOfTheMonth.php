<?php

declare(strict_types=1);

namespace App\Transfer;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use craft\elements\Asset;
use craft\elements\Entry;
use DateTime;
use DateTimeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

use function array_map;
use function json_encode;

use const JSON_PRETTY_PRINT;

class GetTransferHymnsOfTheMonth
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get(
            '/transfer/hymns-of-the-month',
            self::class,
        );
    }

    public function __construct(
        private EntryQueryFactory $queryFactory,
        private GenericHandler $genericHandler,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $query = $this->queryFactory->make();

        $query->section('hymnsOfTheMonth');

        // Include disabled and future-dated (pending) entries so the whole
        // archive migrates; the enabled flag is carried across per entry.
        $query->status(null);

        $entries = $query->all();

        $response->getBody()->write((string) json_encode(
            array_map(
                fn (Entry $entry): array => $this->transferEntry(entry: $entry),
                $entries,
            ),
            JSON_PRETTY_PRINT,
        ));

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }

    /**
     * The slug and title are intentionally omitted — the API derives both from
     * the date. The asset `path` values already embed the Craft slug, so the
     * sheet and practice-track files resolve unchanged after migration.
     *
     * @return array{
     *     id: string,
     *     date: string,
     *     enabled: bool,
     *     hymnPsalmName: string,
     *     musicSheetFilePath: string|null,
     *     practiceTracks: array<array-key, array{title: string, path: string}>,
     * }
     *
     * @phpstan-ignore-next-line
     */
    private function transferEntry(Entry $entry): array
    {
        $musicSheet = $entry->hymnOfTheMonthMusic->one();

        $practiceTracks = $entry->hymnOfTheMonthPracticeTracks->all();

        return [
            'id' => (string) $entry->uid,
            'date' => $this->date(entry: $entry)->format('Y-m-d H:i:s'),
            'enabled' => $entry->getStatus() !== Entry::STATUS_DISABLED,
            'hymnPsalmName' => $this->genericHandler->getString(
                element: $entry,
                field: 'hymnPsalmName',
            ),
            'musicSheetFilePath' => $musicSheet?->path,
            'practiceTracks' => array_map(
                static fn (Asset $track): array => [
                    'title' => (string) $track->title,
                    'path' => $track->path,
                ],
                $practiceTracks,
            ),
        ];
    }

    private function date(Entry $entry): DateTimeInterface
    {
        $dateField = $entry->getFieldValue('date');

        if ($dateField instanceof DateTimeInterface) {
            return $dateField;
        }

        return $entry->postDate ?? new DateTime('now');
    }
}
