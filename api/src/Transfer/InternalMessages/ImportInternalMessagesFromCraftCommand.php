<?php

declare(strict_types=1);

namespace App\Transfer\InternalMessages;

use App\EmptyUuid;
use App\InternalMessages\InternalMessage;
use App\InternalMessages\InternalMessagesRepository;
use App\InternalMessages\NewInternalMessage;
use Config\RuntimeConfigOptions;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Hyperf\Guzzle\ClientFactory;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use RxAnte\AppBootstrap\RuntimeConfig;
use Throwable;

use function array_map;
use function json_decode;

readonly class ImportInternalMessagesFromCraftCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            expression: 'transfer:import:internal-messages',
            callable: self::class,
        );
    }

    public function __construct(
        private RuntimeConfig $config,
        private InternalMessagesRepository $repository,
        private ClientFactory $guzzleClientFactory,
    ) {
    }

    public function __invoke(): int
    {
        $response = $this->guzzleClientFactory->create()->get(
            $this->config->getString(
                RuntimeConfigOptions::APP_API_URL,
            ) . '/transfer/internal-messages',
        );

        /**
         * @var array{
         *     id: string,
         *     date: string,
         *     title: string,
         *     slug: string,
         *     audioPath: string,
         *     audioFileSize: int,
         *     speakerId: string,
         *     passage: string,
         *     seriesId: string,
         *     description: string,
         * } $json
         */
        $json = json_decode(
            $response->getBody()->getContents(),
            true,
        );

        $existingMessages = $this->repository->findAll();

        array_map(
            function (array $message) use ($existingMessages): void {
                $id = Uuid::fromString(uuid: $message['id']);

                $date = DateTimeImmutable::createFromFormat(
                    'Y-m-d H:i:s',
                    $message['date'],
                    new DateTimeZone('US/Central'),
                );

                if ($date === false) {
                    return;
                }

                $existing = $existingMessages->findById(id: $id);

                if ($existing !== null) {
                    $this->correctDateIfChanged(
                        existing: $existing,
                        date: $date,
                    );

                    return;
                }

                try {
                    $speakerId = Uuid::fromString(
                        $message['speakerId'] ?? '',
                    );
                } catch (Throwable) {
                    $speakerId = new EmptyUuid();
                }

                try {
                    $seriesId = Uuid::fromString(
                        $message['seriesId'] ?? '',
                    );
                } catch (Throwable) {
                    $seriesId = new EmptyUuid();
                }

                $this->repository->create(new NewInternalMessage(
                    id: $id,
                    date: $date,
                    title: $message['title'] ?? '',
                    audioPath: $message['audioPath'] ?? '',
                    audioFileSize: (int) ($message['audioFileSize'] ?? 0),
                    speakerId: $speakerId,
                    passage: $message['passage'] ?? '',
                    seriesId: $seriesId,
                    description: $message['description'] ?? '',
                    slug: $message['slug'] ?? null,
                ));
            },
            $json,
        );

        return 0;
    }

    private function correctDateIfChanged(
        InternalMessage $existing,
        DateTimeInterface $date,
    ): void {
        $format = 'Y-m-d H:i:s';

        if ($existing->date->format($format) === $date->format($format)) {
            return;
        }

        $this->repository->persist(
            message: $existing->withDate(value: $date),
        );
    }
}
