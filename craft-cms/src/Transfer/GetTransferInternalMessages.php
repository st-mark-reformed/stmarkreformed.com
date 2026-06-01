<?php

declare(strict_types=1);

namespace App\Transfer;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Entry;
use DateTimeZone;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

use function array_map;
use function json_encode;

use const JSON_PRETTY_PRINT;

class GetTransferInternalMessages
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get(
            '/transfer/internal-messages',
            self::class,
        );
    }

    public function __construct(private EntryQueryFactory $queryFactory)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $query = $this->queryFactory->make();

        $query->section('internalMessages');

        $query->with([
            'internalAudio',
            'profile',
            'internalMessageSeries',
        ]);

        $messages = $query->all();

        $response->getBody()->write((string) json_encode(
            array_map(
                static function (Entry $entry): array {
                    $audioAsset = $entry->internalAudio[0] ?? null;

                    $speakerEntry = $entry->profile[0] ?? null;

                    $seriesCategory = $entry->internalMessageSeries[0] ?? null;

                    return [
                        'id' => $entry->uid,
                        'date' => $entry->postDate
                            ->setTimezone(new DateTimeZone('US/Central'))
                            ->format(
                                'Y-m-d H:i:s'
                            ),
                        'title' => $entry->title,
                        'slug' => $entry->slug,
                        'audioPath' => $audioAsset?->filename ?? '',
                        'audioFileSize' => (int) ($audioAsset?->size ?? 0),
                        'speakerId' => $speakerEntry?->uid ?? '',
                        'passage' => $entry->messageText ?? '',
                        'seriesId' => $seriesCategory?->uid ?? '',
                        'description' => $entry->shortDescription ?? '',
                    ];
                },
                $messages,
            ),
            JSON_PRETTY_PRINT,
        ));

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}
