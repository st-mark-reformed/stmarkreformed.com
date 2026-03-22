<?php

declare(strict_types=1);

namespace App\Profiles;

enum ProfileLeadershipPosition
{
    case none;
    case pastor;
    case associate_pastor;
    case assistant_pastor;
    case elder;
    case ruling_elder;
    case deacon;
}
