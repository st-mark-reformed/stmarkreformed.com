<?php

declare(strict_types=1);

namespace App\Transfer\Profiles;

use App\Profiles\NewProfile;
use App\Profiles\ProfileEmail;
use App\Profiles\ProfileLeadershipPosition;
use App\Profiles\ProfilesRepository;
use Config\RuntimeConfigOptions;
use Hyperf\Guzzle\ClientFactory;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use RxAnte\AppBootstrap\RuntimeConfig;

use function array_map;
use function json_decode;

readonly class ImportProfilesFromCraftCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            expression: 'transfer:import:profiles',
            callable: self::class,
        );
    }

    public function __construct(
        private RuntimeConfig $config,
        private ProfilesRepository $repository,
        private ClientFactory $guzzleClientFactory,
    ) {
    }

    public function __invoke(): int
    {
        $response = $this->guzzleClientFactory->create()->get(
            $this->config->getString(
                RuntimeConfigOptions::APP_API_URL,
            ) . '/transfer/profiles',
        );

        /**
         * @var array{
         *     id: string,
         *     titleOrHonorific: string,
         *     firstName: string,
         *     lastName: string,
         *     email: string,
         *     leadershipPosition: string,
         *     bio: string,
         *     hasMessages: bool,
         * } $profilesJson
         */
        $profilesJson = json_decode(
            $response->getBody()->getContents(),
            true,
        );

        $existingProfiles = $this->repository->findAll();

        array_map(
            function (array $profile) use ($existingProfiles): void {
                $id = Uuid::fromString(uuid: $profile['id']);

                $existingProfile = $existingProfiles->findById(id: $id);

                if ($existingProfile !== null) {
                    return;
                }

                $this->repository->create(profile: new NewProfile(
                    id: $id,
                    titleOrHonorific: $profile['titleOrHonorific'] ?? '',
                    firstName: $profile['firstName'] ?? '',
                    lastName: $profile['lastName'] ?? '',
                    email: new ProfileEmail(email: $profile['email'] ?? ''),
                    leadershipPosition: ProfileLeadershipPosition::fromStringSafe(
                        type: $profile['leadershipPosition'] ?? '',
                    ),
                    bio: $profile['bio'] ?? '',
                    hasMessages: (bool) $profile['hasMessages'],
                ));
            },
            $profilesJson,
        );

        return 0;
    }
}
