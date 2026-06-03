<?php

declare(strict_types=1);

namespace App\Transfer;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\EntryBuilder\ExtractBodyContent;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\Matrix\MatrixFieldHandler;
use craft\elements\Entry;
use DateTime;
use DateTimeZone;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

use function array_map;
use function json_encode;

use const JSON_PRETTY_PRINT;

class GetTransferNews
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get(
            '/transfer/news',
            self::class,
        );
    }

    public function __construct(
        private EntryQueryFactory $queryFactory,
        private MatrixFieldHandler $matrixFieldHandler,
        private GenericHandler $genericHandler,
        private ExtractBodyContent $extractBodyContent,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $query = $this->queryFactory->make();

        $query->section('news');

        // Include disabled and future-dated (pending) entries so the whole
        // archive migrates; the enabled flag is carried across per entry.
        $query->status(null);

        $query->with(['entryBuilder']);

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
     *     heading: string,
     *     subheading: string,
     *     body: string,
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
            'heading' => $this->firstBlockField(entry: $entry, field: 'heading'),
            'subheading' => $this->firstBlockField(
                entry: $entry,
                field: 'subheading',
            ),
            'body' => $this->extractBodyContent->fromElementWithEntryBuilder(
                element: $entry,
            ),
        ];
    }

    private function firstBlockField(Entry $entry, string $field): string
    {
        $blocks = $this->matrixFieldHandler->getAll(
            element: $entry,
            field: 'entryBuilder',
        );

        foreach ($blocks as $block) {
            if ($block->getType()->handle !== 'basicEntryBlock') {
                continue;
            }

            return $this->genericHandler->getString(
                element: $block,
                field: $field,
            );
        }

        return '';
    }
}
