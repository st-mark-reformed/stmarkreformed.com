<?php

declare(strict_types=1);

namespace App\Craft\Behaviors;

use craft\elements\Entry;
use craft\fields\data\SingleOptionFieldData;
use Throwable;
use yii\base\Behavior;

use function assert;

/**
 * @property Entry $owner
 */
class ProfileEntriesBehavior extends Behavior
{
    public function fullNameHonorific(): string
    {
        try {
            $titleOrHonorific = (string) $this->owner->getFieldValue(
                'titleOrHonorific',
            );
        } catch (Throwable) {
            $titleOrHonorific = '';
        }

        if ($titleOrHonorific === '') {
            return (string) $this->owner->title;
        }

        return $titleOrHonorific . ' ' . ((string) $this->owner->title);
    }

    public function fullNameHonorificAppendedPosition(): string
    {
        $title = $this->fullNameHonorific();

        try {
            $position = $this->owner->getFieldValue(
                'leadershipPosition'
            );

            assert($position instanceof SingleOptionFieldData);

            $positionString = (string) $position->label;

            if ($positionString === '') {
                return $title;
            }

            return $title . ' (' . $positionString . ')';
        } catch (Throwable) {
            return $title;
        }
    }
}
