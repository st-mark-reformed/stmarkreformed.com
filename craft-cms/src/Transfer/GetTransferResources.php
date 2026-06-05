<?php

declare(strict_types=1);

namespace App\Transfer;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\Matrix\MatrixFieldHandler;
use craft\elements\Asset;
use craft\elements\Entry;
use DateTime;
use DateTimeZone;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

use function array_map;
use function json_encode;

use const JSON_PRETTY_PRINT;

class GetTransferResources
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get(
            '/transfer/resources',
            self::class,
        );
    }

    public function __construct(
        private EntryQueryFactory $queryFactory,
        private GenericHandler $genericHandler,
        private AssetsFieldHandler $assetsFieldHandler,
        private MatrixFieldHandler $matrixFieldHandler,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $query = $this->queryFactory->make();

        $query->section('resources');

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
     * @return array{
     *     id: string,
     *     date: string,
     *     title: string,
     *     slug: string,
     *     enabled: bool,
     *     body: string,
     *     resourceDownloads: array<int, array{filename: string}>,
     * }
     *
     * @phpstan-ignore-next-line
     */
    private function transferEntry(Entry $entry): array
    {
        $postDate = $entry->postDate ?? $entry->dateCreated ?? new DateTime('now');

        return [
            'id' => (string) $entry->uid,
            'date' => $postDate
                ->setTimezone(new DateTimeZone('US/Central'))
                ->format('Y-m-d H:i:s'),
            'title' => (string) $entry->title,
            'slug' => (string) $entry->slug,
            'enabled' => $entry->getStatus() !== Entry::STATUS_DISABLED,
            'body' => $this->genericHandler->getString(
                element: $entry,
                field: 'body',
            ),
            'resourceDownloads' => $this->resourceDownloads(entry: $entry),
        ];
    }

    /** @return array<int, array{filename: string}> */
    private function resourceDownloads(Entry $entry): array
    {
        $blocks = $this->matrixFieldHandler->getAll(
            element: $entry,
            field: 'resourceDownloads',
        );

        $downloads = [];

        foreach ($blocks as $block) {
            $asset = $this->assetsFieldHandler->getOneOrNull(
                element: $block,
                field: 'file',
            );

            if (! $asset instanceof Asset) {
                continue;
            }

            $downloads[] = ['filename' => $asset->getFilename()];
        }

        return $downloads;
    }
}
