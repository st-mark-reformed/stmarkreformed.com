<?php

declare(strict_types=1);

namespace App\Profiles;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

readonly class ResaveAllProfilesCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'profiles:resave-all',
            self::class,
        );
    }

    public function __construct(private ProfilesRepository $repository)
    {
    }

    public function __invoke(): int
    {
        $this->repository->findAll()->map(
            callback: function (Profile $profile): void {
                $this->repository->persist(profile: $profile);
            },
        );

        return 0;
    }
}
