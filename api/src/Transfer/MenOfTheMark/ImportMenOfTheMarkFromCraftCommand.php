<?php

declare(strict_types=1);

namespace App\Transfer\MenOfTheMark;

use App\MenOfTheMark\MenOfTheMarkItem;
use App\MenOfTheMark\MenOfTheMarkRepository;
use App\MenOfTheMark\NewMenOfTheMarkItem;
use Config\RuntimeConfigOptions;
use DateTimeImmutable;
use DateTimeZone;
use Hyperf\Guzzle\ClientFactory;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use RxAnte\AppBootstrap\RuntimeConfig;

use function array_map;
use function json_decode;

readonly class ImportMenOfTheMarkFromCraftCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            expression: 'transfer:import:men-of-the-mark',
            callable: self::class,
        );
    }

    public function __construct(
        private RuntimeConfig $config,
        private MenOfTheMarkRepository $repository,
        private ClientFactory $guzzleClientFactory,
    ) {
    }

    public function __invoke(): int
    {
        $response = $this->guzzleClientFactory->create()->get(
            $this->config->getString(
                RuntimeConfigOptions::APP_API_URL,
            ) . '/transfer/men-of-the-mark',
        );

        /**
         * @var array<array{
         *     id: string,
         *     date: string,
         *     title: string,
         *     slug: string,
         *     enabled: bool,
         *     body: string,
         * }> $json
         */
        $json = json_decode(
            $response->getBody()->getContents(),
            true,
        );

        $existingItems = $this->repository->findAll();

        array_map(
            function (array $item) use ($existingItems): void {
                $id = Uuid::fromString(uuid: $item['id']);

                $date = DateTimeImmutable::createFromFormat(
                    'Y-m-d H:i:s',
                    $item['date'],
                    new DateTimeZone('US/Central'),
                );

                if ($date === false) {
                    return;
                }

                $existing = $existingItems->findById(id: $id);

                if ($existing === null) {
                    $this->repository->create(new NewMenOfTheMarkItem(
                        isEnabled: $item['enabled'],
                        date: $date,
                        title: $item['title'],
                        // Preserve the Craft slug so existing permalinks hold.
                        slug: $item['slug'],
                        body: $item['body'],
                        id: $id,
                    ));

                    return;
                }

                $this->syncIfChanged(
                    existing: $existing,
                    incoming: new MenOfTheMarkItem(
                        id: $id,
                        isEnabled: $item['enabled'],
                        date: $date,
                        title: $item['title'],
                        slug: $item['slug'],
                        body: $item['body'],
                    ),
                );
            },
            $json,
        );

        return 0;
    }

    private function syncIfChanged(
        MenOfTheMarkItem $existing,
        MenOfTheMarkItem $incoming,
    ): void {
        if ($existing->asArray() === $incoming->asArray()) {
            return;
        }

        $this->repository->persist(menOfTheMarkItem: $incoming);
    }
}
