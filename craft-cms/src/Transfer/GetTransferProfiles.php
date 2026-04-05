<?php

declare(strict_types=1);

namespace App\Transfer;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Entry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

use function array_map;
use function json_encode;

use const JSON_PRETTY_PRINT;

class GetTransferProfiles
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get(
            '/transfer/profiles',
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

        $query->section('profiles');

        $profiles = $query->all();

        $response->getBody()->write((string) json_encode(
            array_map(
                static function (Entry $entry): array {
                    return [
                        'id' => $entry->uid,
                        'titleOrHonorific' => $entry->titleOrHonorific,
                        'firstName' => $entry->firstName,
                        'lastName' => $entry->lastName,
                        'email' => $entry->email,
                        'leadershipPosition' => $entry->leadershipPosition->value,
                        'bio' => (string) $entry->bio,
                        'hasMessages' => (bool) $entry->hasMessages,
                    ];
                },
                $profiles,
            ),
            JSON_PRETTY_PRINT,
        ));

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}
