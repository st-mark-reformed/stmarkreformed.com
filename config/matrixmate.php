<?php

declare(strict_types=1);

return [
    'fields' => [
        'pageBuilder' => [
            'groups' => [
                [
                    'label' => 'Content',
                    'types' => ['basicBlock'],
                ],
                [
                    'label' => 'CTAs',
                    'types' => [
                        'simpleCta',
                        'imageContentCta',
                    ],
                ],
                [
                    'label' => 'Features',
                    'types' => [
                        'latestGalleries',
                        'featuredSermonSeries',
                    ],
                ],
            ],
            // 'types' => [
            //     'imageContentCta' => [
            //         'tabs' => [
            //             [
            //                 'label' => 'Test 1',
            //                 'fields' => ['preHeadline', 'Headline'],
            //             ],
            //             [
            //                 'label' => 'Test 2',
            //                 'fields' => ['contentField'],
            //             ],
            //         ],
            //         'hiddenFields' => ['backgroundColor'],
            //     ],
            //     'news' => ['maxLimit' => 1],
            // ],
        ],
    ],
];
