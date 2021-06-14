<?php

return [
    'freee_api_base_url' => 'https://api.freee.co.jp/api/1',

    'browser_pusher' => [
        'pusher_key' => 'f1c1e963a949d2a7f0c9a8bc8905220f',
        'pusher_auth_token' => '30044'
    ],

    'company' => [
        'types' => [
            [
                'id' => 'corporate',
                'name' => 'Corporate',
            ],
            [
                'id' => 'proprietary',
                'name' => 'Proprietary',
            ],
        ],

        'statuses' => [
            [
                'id' => 0,
                'name' => 'Disconnect',
            ],
            [
                'id' => 1,
                'name' => 'Connect',
            ],
        ],
    ],

    'account_item' => [
        'types' => [
            [
                'id' => 'expense',
                'name' => 'Expense',
            ],
            [
                'id' => 'income',
                'name' => 'Income',
            ],
        ],

        'subtypes' => [
            [
                'id' => 'F',
                'name' => 'Food',
            ],
            [
                'id' => 'L',
                'name' => 'Labor',
            ],
            [
                'id' => 'O',
                'name' => 'Other',
            ],
        ],
    ],

    'notification' => [
        'types' => [
            [
                'id' => 'general',
                'name' => 'お知らせ通知',
            ],
            [
                'id' => 'important',
                'name' => '重要なお知らせ',
            ],
            [
                'id' => 'promotion',
                'name' => '一般通知',
            ],
        ],
    ],

    'user' => [
        'preferences' => [
            'notification' => [
                [
                    'id' => 'notification.promotion',
                    'name' => '一般通知',
                    'description' => '新着記事、助成金情報などお得情報が投稿された際にメールでの通知を希望します。',
                ],
                [
                    'id' => 'notification.general',
                    'name' => 'お知らせ通知',
                    'description' => 'お知らせ投稿があった際にメールで通知を希望します。',
                ],
                [
                    'id' => 'notification.important',
                    'name' => '重要なお知らせ',
                    'description' => 'システムに関する重要なお知らせを通知します。',
                ],
                [
                    'id' => 'notification.browser',
                    'name' => 'Browser',
                    'description' => 'Is browser notification allow or not',
                ],
            ],
        ],
    ],
];
