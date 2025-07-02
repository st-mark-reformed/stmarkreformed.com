<?php

declare(strict_types=1);

namespace App\Messages\ImportFromCraft;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

use function array_map;
use function array_merge;
use function count;

// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification

readonly class ImportMessagesFromCraftCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'messages:import-from-craft',
            self::class,
        );
    }

    public function __construct(
        private ImportItem $importItem,
        private RedisRepository $redisRepository,
    ) {
    }

    public function __invoke(): int
    {
        $firstPageData = $this->redisRepository->findAllMessagesByPage(1);

        if ($firstPageData === null) {
            return 0;
        }

        $totalPages = $firstPageData['totalPages'];

        $allItems = [...$firstPageData['entries']];

        for ($pageNum = 2; $pageNum <= $totalPages; $pageNum++) {
            $pageData = $this->redisRepository->findAllMessagesByPage(
                $pageNum,
            );

            if ($pageData === null || count($pageData['entries']) <= 0) {
                continue;
            }

            $allItems = array_merge($allItems, $pageData['entries']);
        }

        array_map(
            [$this->importItem, 'import'],
            $allItems,
        );

        return 0;
    }
}
