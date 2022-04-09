<?php

declare(strict_types=1);

return [
    'fields' => [
        'pageBuilder' => [
            'groups' => [
                [
                    'label' => 'Content',
                    'types' => [
                        'basicBlock',
                        'contactForm',
                        'stripePaymentForm',
                    ],
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
                        'upcomingEvents',
                        'latestGalleries',
                        'featuredSermonSeries',
                    ],
                ],
                [
                    'label' => 'Pre-defined',
                    'types' => ['leadership'],
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
