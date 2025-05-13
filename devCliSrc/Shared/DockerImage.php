<?php

declare(strict_types=1);

namespace Cli\Shared;

use RuntimeException;
use Throwable;

use function array_map;
use function array_values;
use function constant;
use function implode;

enum DockerImage
{
    case api;
    case apiQueueConsumer;
    case apiScheduleRunner;
    case app;
    case appScheduleRunner;
    case db;
    case proxy;
    case utility;
    case web;

    /**
     * @param array<array-key, string|DockerImage>|null $images
     *
     * @return self[]
     */
    public static function fromArray(array|null $images = null): array
    {
        if ($images === null) {
            return self::cases();
        }

        return array_values(array_map(
            static function (string|self $image): DockerImage {
                if ($image instanceof self) {
                    return $image;
                }

                try {
                    return constant(self::class . '::' . $image);
                } catch (Throwable) {
                    throw new RuntimeException(
                        $image . ' is not a valid image',
                    );
                }
            },
            $images,
        ));
    }

    public function getDashCaseName(): string
    {
        return match($this->name) {
            'apiQueueConsumer' => 'api-queue-consumer',
            'apiScheduleRunner' => 'api-schedule-runner',
            'appScheduleRunner' => 'app-schedule-runner',
            default => $this->name,
        };
    }

    public function tag(): string
    {
        return implode('', [
            'ghcr.io/st-mark-reformed/stmarkreformed.com-',
            $this->getDashCaseName(),
        ]);
    }

    public function dockerfilePath(): string
    {
        $dir = match($this->name) {
            'app' => 'application',
            'appScheduleRunner' => 'schedule-runner',
            default => $this->getDashCaseName(),
        };

        return implode('/', [
            'docker',
            $dir,
            'Dockerfile',
        ]);
    }
}
