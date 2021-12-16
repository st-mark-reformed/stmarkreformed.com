<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth;

use App\Http\Response\Members\HymnsOfTheMonth\Response\HymnsOfTheMonthResponderFactory;
use App\Http\RouteMiddleware\RequireLogIn\RequireLogInMiddleware;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

class HymnsOfTheMonthAction
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector
            ->get(
                '/members/hymns-of-the-month',
                self::class,
            )
            ->setArgument(
                'pageTitle',
                'Log in to view the members area'
            )
            ->add(RequireLogInMiddleware::class);
    }

    public function __construct(
        private RetrieveHymns $retrieveHymns,
        private HymnsOfTheMonthResponderFactory $responderFactory,
    ) {
    }

    /**
     * @throws InvalidFieldException
     */
    public function __invoke(): ResponseInterface
    {
        $results = $this->retrieveHymns->retrieve();

        return $this->responderFactory->make(results: $results)->respond(
            results: $results,
        );
    }
}
