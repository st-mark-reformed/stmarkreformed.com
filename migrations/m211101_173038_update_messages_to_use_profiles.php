<?php

declare(strict_types=1);

namespace craft\contentmigrations;

use Craft;
use craft\db\Migration;
use craft\elements\db\UserQuery;
use craft\elements\Entry;
use craft\errors\ElementNotFoundException;
use craft\errors\InvalidFieldException;
use Throwable;
use yii\base\Exception;

use function array_map;
use function assert;

// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps

class m211101_173038_update_messages_to_use_profiles extends Migration
{
    public function safeUp(): bool
    {
        /**
         * @var Entry[] $messages
         * @phpstan-ignore-next-line
         */
        $messages = Entry::find()->section([
            'messages',
            'internalMessages',
        ])->anyStatus()->all();

        array_map(
            [$this, 'updateMessage'],
            $messages,
        );

        return true;
    }

    /**
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws InvalidFieldException
     * @throws Exception
     */
    private function updateMessage(Entry $entry): void
    {
        $userQuery = $entry->getFieldValue('speaker');

        assert($userQuery instanceof UserQuery);

        $user = $userQuery->anyStatus()->one();

        if ($user === null) {
            return;
        }

        $profile = Entry::find()->section('profiles')->relatedTo([
            'targetElement' => $user,
            'field' => 'associatedUserAccount',
        ])->one();

        if ($profile === null) {
            return;
        }

        $entry->setFieldValue(
            'profile',
            /** @phpstan-ignore-next-line */
            [$profile->id],
        );

        /** @phpstan-ignore-next-line */
        Craft::$app->getElements()->saveElement($entry);
    }

    public function safeDown(): bool
    {
        echo "m211101_173038_update_messages_to_use_profiles cannot be reverted.\n";

        return false;
    }
}
