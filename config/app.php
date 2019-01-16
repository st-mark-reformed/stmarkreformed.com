<?php
declare(strict_types=1);

use dev\Module;

return [
    'modules' => [
        'dev' => Module::class,
    ],
    'bootstrap' => [
        'dev'
    ],
];
