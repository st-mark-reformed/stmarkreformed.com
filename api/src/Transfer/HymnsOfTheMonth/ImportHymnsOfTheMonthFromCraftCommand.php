<?php

declare(strict_types=1);

namespace App\Transfer\HymnsOfTheMonth;

use App\HymnsOfTheMonth\HymnOfTheMonthItem;
use App\HymnsOfTheMonth\HymnPracticeTracks;
use App\HymnsOfTheMonth\HymnsOfTheMonthRepository;
use App\HymnsOfTheMonth\NewHymnOfTheMonthItem;
use Config\RuntimeConfigOptions;
use DateTimeImmutable;
use DateTimeZone;
use Hyperf\Guzzle\ClientFactory;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use RxAnte\AppBootstrap\RuntimeConfig;

use function array_map;
use function json_decode;

readonly class ImportHymnsOfTheMonthFromCraftCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            expression: 'transfer:import:hymns-of-the-month',
            callable: self::class,
        );
    }

    public function __construct(
        private RuntimeConfig $config,
        private HymnsOfTheMonthRepository $repository,
        private ClientFactory $guzzleClientFactory,
    ) {
    }

    public function __invoke(): int
    {
        $response = $this->guzzleClientFactory->create()->get(
            $this->config->getString(
                RuntimeConfigOptions::APP_API_URL,
            ) . '/transfer/hymns-of-the-month',
        );

        /**
         * @var array<array{
         *     id: string,
         *     date: string,
         *     enabled: bool,
         *     hymnPsalmName: string,
         *     musicSheetFilePath: string|null,
         *     practiceTracks: array<array-key, array{title: string, path: string}>,
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

                $musicSheetPath = (string) ($item['musicSheetFilePath'] ?? '');

                $practiceTracks = HymnPracticeTracks::fromArray(
                    raw: $item['practiceTracks'] ?? [],
                );

                $existing = $existingItems->findById(id: $id);

                if ($existing === null) {
                    $this->repository->create(new NewHymnOfTheMonthItem(
                        isEnabled: $item['enabled'],
                        date: $date,
                        hymnPsalmName: $item['hymnPsalmName'],
                        musicSheetPath: $musicSheetPath,
                        practiceTracks: $practiceTracks,
                        id: $id,
                    ));

                    return;
                }

                $this->syncIfChanged(
                    existing: $existing,
                    incoming: new HymnOfTheMonthItem(
                        id: $id,
                        isEnabled: $item['enabled'],
                        date: $date,
                        hymnPsalmName: $item['hymnPsalmName'],
                        musicSheetPath: $musicSheetPath,
                        practiceTracks: $practiceTracks,
                    ),
                );
            },
            $json,
        );

        return 0;
    }

    private function syncIfChanged(
        HymnOfTheMonthItem $existing,
        HymnOfTheMonthItem $incoming,
    ): void {
        if ($existing->asArray() === $incoming->asArray()) {
            return;
        }

        $this->repository->persist(hymnOfTheMonthItem: $incoming);
    }
}
