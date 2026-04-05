<?php

declare(strict_types=1);

namespace App\Transfer\Series;

use App\Series\NewSeries;
use App\Series\SeriesRepository;
use App\Series\SeriesSlug;
use Config\RuntimeConfigOptions;
use Hyperf\Guzzle\ClientFactory;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use RxAnte\AppBootstrap\RuntimeConfig;

use function array_map;
use function json_decode;

readonly class ImportSeriesFromCraftCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            expression: 'transfer:import:series',
            callable: self::class,
        );
    }

    public function __construct(
        private RuntimeConfig $config,
        private SeriesRepository $repository,
        private ClientFactory $guzzleClientFactory,
    ) {
    }

    public function __invoke(): int
    {
        $response = $this->guzzleClientFactory->create()->get(
            $this->config->getString(
                RuntimeConfigOptions::APP_API_URL,
            ) . '/transfer/series',
        );

        /**
         * @var array{
         *     id: string,
         *     title: string,
         *     slug: string,
         * } $json
         */
        $json = json_decode(
            $response->getBody()->getContents(),
            true,
        );

        $existingSeries = $this->repository->findAll();

        array_map(
            function (array $series) use ($existingSeries): void {
                $id = Uuid::fromString(uuid: $series['id']);

                $existingSeries = $existingSeries->findById(id: $id);

                if ($existingSeries !== null) {
                    return;
                }

                $this->repository->create(series: new NewSeries(
                    id: $id,
                    title: $series['title'],
                    slug: new SeriesSlug(slug: $series['slug'] ?? ''),
                ));
            },
            $json,
        );

        return 0;
    }
}
