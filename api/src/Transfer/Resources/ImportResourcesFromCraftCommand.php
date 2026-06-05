<?php

declare(strict_types=1);

namespace App\Transfer\Resources;

use App\Resources\NewResourceItem;
use App\Resources\ResourceDownloads;
use App\Resources\ResourceItem;
use App\Resources\ResourcesRepository;
use Config\RuntimeConfigOptions;
use DateTimeImmutable;
use DateTimeZone;
use Hyperf\Guzzle\ClientFactory;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use RxAnte\AppBootstrap\RuntimeConfig;

use function array_map;
use function json_decode;

readonly class ImportResourcesFromCraftCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            expression: 'transfer:import:resources',
            callable: self::class,
        );
    }

    public function __construct(
        private RuntimeConfig $config,
        private ResourcesRepository $repository,
        private ClientFactory $guzzleClientFactory,
    ) {
    }

    public function __invoke(): int
    {
        $response = $this->guzzleClientFactory->create()->get(
            $this->config->getString(
                RuntimeConfigOptions::APP_API_URL,
            ) . '/transfer/resources',
        );

        /**
         * @var array<array{
         *     id: string,
         *     date: string,
         *     title: string,
         *     slug: string,
         *     enabled: bool,
         *     body: string,
         *     resourceDownloads: array<array-key, array{filename: string}>,
         * }> $json
         */
        $json = json_decode(
            $response->getBody()->getContents(),
            true,
        );

        $existingResources = $this->repository->findAll();

        array_map(
            function (array $item) use ($existingResources): void {
                $id = Uuid::fromString(uuid: $item['id']);

                $date = DateTimeImmutable::createFromFormat(
                    'Y-m-d H:i:s',
                    $item['date'],
                    new DateTimeZone('US/Central'),
                );

                if ($date === false) {
                    return;
                }

                $downloads = ResourceDownloads::fromArray(
                    raw: $item['resourceDownloads'] ?? [],
                );

                $existing = $existingResources->findById(id: $id);

                if ($existing === null) {
                    $this->repository->create(new NewResourceItem(
                        isEnabled: $item['enabled'],
                        date: $date,
                        title: $item['title'],
                        // Preserve the Craft slug so existing permalinks and
                        // file paths hold.
                        slug: $item['slug'],
                        body: $item['body'],
                        downloads: $downloads,
                        id: $id,
                    ));

                    return;
                }

                $this->syncIfChanged(
                    existing: $existing,
                    incoming: new ResourceItem(
                        id: $id,
                        isEnabled: $item['enabled'],
                        date: $date,
                        title: $item['title'],
                        slug: $item['slug'],
                        body: $item['body'],
                        downloads: $downloads,
                    ),
                );
            },
            $json,
        );

        return 0;
    }

    private function syncIfChanged(
        ResourceItem $existing,
        ResourceItem $incoming,
    ): void {
        if ($existing->asArray() === $incoming->asArray()) {
            return;
        }

        $this->repository->persist(resourceItem: $incoming);
    }
}
