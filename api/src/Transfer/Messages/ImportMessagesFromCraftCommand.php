<?php

declare(strict_types=1);

namespace App\Transfer\Messages;

use App\EmptyUuid;
use App\Messages\MessagesRepository;
use App\Messages\NewMessage;
use Config\RuntimeConfigOptions;
use DateTimeImmutable;
use DateTimeZone;
use Hyperf\Guzzle\ClientFactory;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use RxAnte\AppBootstrap\RuntimeConfig;
use Throwable;

use function array_map;
use function json_decode;

readonly class ImportMessagesFromCraftCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            expression: 'transfer:import:messages',
            callable: self::class,
        );
    }

    public function __construct(
        private RuntimeConfig $config,
        private MessagesRepository $repository,
        private ClientFactory $guzzleClientFactory,
    ) {
    }

    public function __invoke(): int
    {
        $response = $this->guzzleClientFactory->create()->get(
            $this->config->getString(
                RuntimeConfigOptions::APP_API_URL,
            ) . '/transfer/messages',
        );

        /**
         * @var array{
         *     id: string,
         *     date: string,
         *     title: string,
         *     audioPath: string,
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

                $existing = $existingMessages->findById(id: $id);

                if ($existing !== null) {
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

                $this->repository->create(new NewMessage(
                    id: $id,
                    date: DateTimeImmutable::createFromFormat(
                        'Y-m-d H:i:s',
                        $message['date'],
                        new DateTimeZone('US/Central'),
                    ),
                    title: $message['title'] ?? '',
                    audioPath: $message['audioPath'] ?? '',
                    speakerId: $speakerId,
                    passage: $message['passage'] ?? '',
                    seriesId: $seriesId,
                    description: $message['description'] ?? '',
                ));
            },
            $json,
        );

        return 0;
    }
}
