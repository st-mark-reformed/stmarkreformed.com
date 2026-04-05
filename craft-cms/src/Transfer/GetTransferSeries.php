<?php

declare(strict_types=1);

namespace App\Transfer;

use App\Shared\ElementQueryFactories\CategoryQueryFactory;
use craft\elements\Category;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

use function array_map;
use function json_encode;

use const JSON_PRETTY_PRINT;

class GetTransferSeries
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get(
            '/transfer/series',
            self::class,
        );
    }

    public function __construct(private CategoryQueryFactory $queryFactory)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $query = $this->queryFactory->make();

        $query->group('messageSeries');

        $series = $query->all();

        $response->getBody()->write((string) json_encode(
            array_map(
                static function (Category $category): array {
                    return [
                        'id' => $category->uid,
                        'title' => $category->title,
                        'slug' => $category->slug,
                    ];
                },
                $series,
            ),
            JSON_PRETTY_PRINT,
        ));

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}
