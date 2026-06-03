<?php

declare(strict_types=1);

namespace App\Transfer\News;

use App\News\NewNewsItem;
use App\News\NewsItem;
use App\News\NewsRepository;
use Config\RuntimeConfigOptions;
use DateTimeImmutable;
use DateTimeZone;
use Hyperf\Guzzle\ClientFactory;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use RxAnte\AppBootstrap\RuntimeConfig;

use function array_map;
use function json_decode;

readonly class ImportNewsFromCraftCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            expression: 'transfer:import:news',
            callable: self::class,
        );
    }

    public function __construct(
        private RuntimeConfig $config,
        private NewsRepository $repository,
        private ClientFactory $guzzleClientFactory,
    ) {
    }

    public function __invoke(): int
    {
        $response = $this->guzzleClientFactory->create()->get(
            $this->config->getString(
                RuntimeConfigOptions::APP_API_URL,
            ) . '/transfer/news',
        );

        /**
         * @var array<array{
         *     id: string,
         *     date: string,
         *     title: string,
         *     slug: string,
         *     enabled: bool,
         *     heading: string,
         *     subheading: string,
         *     body: string,
         * }> $json
         */
        $json = json_decode(
            $response->getBody()->getContents(),
            true,
        );

        $existingNews = $this->repository->findAll();

        array_map(
            function (array $item) use ($existingNews): void {
                $id = Uuid::fromString(uuid: $item['id']);

                $date = DateTimeImmutable::createFromFormat(
                    'Y-m-d H:i:s',
                    $item['date'],
                    new DateTimeZone('US/Central'),
                );

                if ($date === false) {
                    return;
                }

                $existing = $existingNews->findById(id: $id);

                if ($existing === null) {
                    $this->repository->create(new NewNewsItem(
                        isEnabled: $item['enabled'],
                        date: $date,
                        title: $item['title'],
                        // Preserve the Craft slug so existing permalinks hold.
                        slug: $item['slug'],
                        heading: $item['heading'],
                        subheading: $item['subheading'],
                        body: $item['body'],
                        id: $id,
                    ));

                    return;
                }

                $this->syncIfChanged(
                    existing: $existing,
                    incoming: new NewsItem(
                        id: $id,
                        isEnabled: $item['enabled'],
                        date: $date,
                        title: $item['title'],
                        slug: $item['slug'],
                        heading: $item['heading'],
                        subheading: $item['subheading'],
                        body: $item['body'],
                    ),
                );
            },
            $json,
        );

        return 0;
    }

    private function syncIfChanged(NewsItem $existing, NewsItem $incoming): void
    {
        if ($existing->asArray() === $incoming->asArray()) {
            return;
        }

        $this->repository->persist(newsItem: $incoming);
    }
}
