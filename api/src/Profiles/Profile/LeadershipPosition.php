<?php

declare(strict_types=1);

namespace App\Profiles\Profile;

enum LeadershipPosition
{
    case NONE;
    case PASTOR;
    case ASSOCIATE_PASTOR;
    case ASSISTANT_PASTOR;
    case ELDER;
    case RULING_ELDER;
    case DEACON;

    case INVALID;

    public static function createFromString(string $position): LeadershipPosition
    {
        $none            = LeadershipPosition::NONE;
        $pastor          = LeadershipPosition::PASTOR;
        $associatePastor = LeadershipPosition::ASSOCIATE_PASTOR;
        $assistantPastor = LeadershipPosition::ASSISTANT_PASTOR;
        $elder           = LeadershipPosition::ELDER;
        $rulingElder     = LeadershipPosition::RULING_ELDER;
        $deacon          = LeadershipPosition::DEACON;

        return match ($position) {
            '', $none->name => $none,
            $pastor->name => $pastor,
            $associatePastor->name => $associatePastor,
            $assistantPastor->name => $assistantPastor,
            $elder->name => $elder,
            $rulingElder->name => $rulingElder,
            $deacon->name => $deacon,
            default => LeadershipPosition::INVALID,
        };
    }

    public static function createFromHumanReadable(
        string $value,
    ): LeadershipPosition {
        $none            = LeadershipPosition::NONE;
        $pastor          = LeadershipPosition::PASTOR;
        $associatePastor = LeadershipPosition::ASSOCIATE_PASTOR;
        $assistantPastor = LeadershipPosition::ASSISTANT_PASTOR;
        $elder           = LeadershipPosition::ELDER;
        $rulingElder     = LeadershipPosition::RULING_ELDER;
        $deacon          = LeadershipPosition::DEACON;

        return match ($value) {
            $none->humanReadable() => $none,
            $pastor->humanReadable() => $pastor,
            $associatePastor->humanReadable() => $associatePastor,
            $assistantPastor->humanReadable() => $assistantPastor,
            $elder->humanReadable() => $elder,
            $rulingElder->humanReadable() => $rulingElder,
            $deacon->humanReadable() => $deacon,
            default => LeadershipPosition::INVALID,
        };
    }

    public function humanReadable(): string
    {
        return match ($this) {
            LeadershipPosition::NONE => '',
            LeadershipPosition::PASTOR => 'Pastor',
            LeadershipPosition::ASSOCIATE_PASTOR => 'Associate Pastor',
            LeadershipPosition::ASSISTANT_PASTOR => 'Assistant Pastor',
            LeadershipPosition::ELDER => 'Elder',
            LeadershipPosition::RULING_ELDER => 'Ruling Elder',
            LeadershipPosition::DEACON => 'Deacon',
            LeadershipPosition::INVALID => 'Invalid',
        };
    }
}
