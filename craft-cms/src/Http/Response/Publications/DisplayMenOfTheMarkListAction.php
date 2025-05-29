<?php

declare(strict_types=1);

namespace App\Http\Response\Publications;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Entities\Meta;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Twig\Environment as TwigEnvironment;

// phpcs:disable Generic.Files.LineLength.TooLong

class DisplayMenOfTheMarkListAction
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get(
            '/publications/men-of-the-mark',
            self::class,
        );
    }

    public function __construct(
        private TwigEnvironment $twig,
        private HeroFactory $heroFactory,
        private FetchMenOfTheMark $fetchMenOfTheMark,
        private GenerateMenOfTheMarkPagesForRedis $generateMenOfTheMarkPagesForRedis,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $this->generateMenOfTheMarkPagesForRedis->generate();

        $pageName = 'Men of the Mark Publications';

        $response->getBody()->write($this->twig->render(
            '@app/Http/Response/Publications/DisplayMenOfTheMark.twig',
            [
                'meta' => new Meta($pageName),
                'hero' => $this->heroFactory->createFromDefaults(
                    heroHeading: $pageName,
                    heroParagraph: 'And then all the host of Rohan burst into song, and they sang as they slew, for the joy of battle was on them, and the sound of their singing that was fair and terrible came even to the City.',
                ),
                'publications' => $this->fetchMenOfTheMark->fetch(),
            ]
        ));

        return $response;
    }
}
