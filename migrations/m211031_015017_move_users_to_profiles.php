<?php

declare(strict_types=1);

namespace craft\contentmigrations;

use Cocur\Slugify\Slugify;
use Craft;
use craft\db\Migration;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\elements\User;
use craft\errors\ElementNotFoundException;
use craft\errors\InvalidFieldException;
use craft\models\Section;
use craft\volumes\Local;
use Throwable;
use yii\base\Exception;
use yii\base\InvalidConfigException;

use function array_map;
use function assert;
use function copy;
use function is_dir;
use function mkdir;

// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps

class m211031_015017_move_users_to_profiles extends Migration
{
    public function safeUp(): bool
    {
        /** @var User[] $users */
        $users = User::find()->anyStatus()->all();

        array_map(
            [$this, 'moveUserToProfile'],
            $users,
        );

        return true;
    }

    /** @phpstan-ignore-next-line */
    private ?Section $section = null;

    /** @phpstan-ignore-next-line */
    private function getSection(): Section
    {
        if ($this->section === null) {
            /** @phpstan-ignore-next-line */
            $this->section = Craft::$app->getSections()->getSectionByHandle(
                'profiles'
            );
        }

        return $this->section;
    }

    /**
     * @throws ElementNotFoundException
     * @throws Exception
     * @throws InvalidConfigException
     * @throws InvalidFieldException
     * @throws Throwable
     *
     * @phpstan-ignore-next-line
     */
    private function moveUserToProfile(User $user): void
    {
        $profile = Entry::find()->relatedTo([
            'targetElement' => $user,
            'field' => 'associatedUserAccount',
        ])->one();

        if ($profile !== null) {
            return;
        }

        $section = $this->getSection();

        $entry = new Entry();

        $entry->authorId = 1;

        $entry->sectionId = $section->id;

        $entry->typeId = $section->getEntryTypes()[0]->id;

        $entry->slug = (string) $user->getFieldValue('slugField');

        if ($entry->slug === '') {
            $entry->slug = (new Slugify())->slugify(
                (string) $user->getFullName()
            );
        }

        $entry->postDate = $user->dateCreated;

        $entry->enabled = true;

        $entry->enabledForSite = true;

        $entry->title = $user->getFullName();

        $entry->setFieldValue(
            'firstName',
            (string) $user->firstName,
        );

        $entry->setFieldValue(
            'lastName',
            (string) $user->lastName,
        );

        $entry->setFieldValue(
            'titleOrHonorific',
            (string) $user->getFieldValue(
                'titleOrHonorific',
            ),
        );

        $entry->setFieldValue(
            'email',
            (string) $user->email,
        );

        $entry->setFieldValue(
            'leadershipPosition',
            (string) $user->getFieldValue(
                'leadershipPosition',
            ),
        );

        $entry->setFieldValue(
            'bio',
            (string) $user->getFieldValue('bio'),
        );

        $entry->setFieldValue(
            'associatedUserAccount',
            [$user->id],
        );

        $userPhoto = $user->getPhoto();

        if ($userPhoto !== null) {
            $profilePhoto = $this->createProfilePhoto(oldPhoto: $userPhoto);

            $entry->setFieldValue(
                'profilePhoto',
                [$profilePhoto->id],
            );
        }

        /** @phpstan-ignore-next-line */
        Craft::$app->getElements()->saveElement($entry);
    }

    /**
     * @throws InvalidConfigException
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     *
     * @phpstan-ignore-next-line
     */
    private function createProfilePhoto(Asset $oldPhoto): Asset
    {
        /** @phpstan-ignore-next-line */
        $newFolder = Craft::$app->getAssets()
            ->findFolder(['name' => 'General']);

        $volume = $oldPhoto->getVolume();

        assert($volume instanceof Local);

        $oldPhotoPath = $volume->getRootPath() . '/' . $oldPhoto->getPath();

        /** @phpstan-ignore-next-line */
        $tmpPhotoDir = CRAFT_BASE_PATH . '/storage/tmp-user-photos';

        $tempPhotoPath = $tmpPhotoDir . '/' . $oldPhoto->getFilename();

        if (! is_dir($tmpPhotoDir)) {
            mkdir(
                $tmpPhotoDir,
                0777,
                true,
            );
        }

        copy($oldPhotoPath, $tempPhotoPath);

        $profilePhoto = new Asset();

        $profilePhoto->tempFilePath = $tempPhotoPath;

        $profilePhoto->filename = $oldPhoto->getFilename();

        $profilePhoto->newFolderId = (int) $newFolder->id;

        $profilePhoto->setVolumeId((int) $newFolder->volumeId);

        $profilePhoto->avoidFilenameConflicts = true;

        $profilePhoto->setScenario(Asset::SCENARIO_CREATE);

        /**
         * @phpstan-ignore-next-line
         */
        Craft::$app->getElements()->saveElement($profilePhoto);

        return $profilePhoto;
    }

    public function safeDown(): bool
    {
        echo "m211031_015017_move_users_to_profiles cannot be reverted.\n";

        return false;
    }
}
