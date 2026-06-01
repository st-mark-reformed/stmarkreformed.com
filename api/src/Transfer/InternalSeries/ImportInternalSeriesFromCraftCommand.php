<?php

declare(strict_types=1);

namespace App\Transfer\InternalSeries;

use App\InternalSeries\InternalSeriesRepository;
use App\InternalSeries\InternalSeriesSlug;
use App\InternalSeries\NewInternalSeries;
use Config\RuntimeConfigOptions;
use Hyperf\Guzzle\ClientFactory;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use RxAnte\AppBootstrap\RuntimeConfig;

use function array_map;
use function json_decode;

readonly class ImportInternalSeriesFromCraftCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            expression: 'transfer:import:internal-series',
            callable: self::class,
        );
    }

    public function __construct(
        private RuntimeConfig $config,
        private InternalSeriesRepository $repository,
        private ClientFactory $guzzleClientFactory,
    ) {
    }

    public function __invoke(): int
    {
        $response = $this->guzzleClientFactory->create()->get(
            $this->config->getString(
                RuntimeConfigOptions::APP_API_URL,
            ) . '/transfer/internal-series',
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

                if ($existingSeries->findById(id: $id) !== null) {
                    return;
                }

                $this->repository->create(series: new NewInternalSeries(
                    id: $id,
                    title: $series['title'],
                    slug: new InternalSeriesSlug(slug: $series['slug'] ?? ''),
                ));
            },
            $json,
        );

        return 0;
    }
}
