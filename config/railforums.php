<?php

/*
 * html_purifier_settings.settings.default array is passed directly in to HTMLPurifier_Config->loadArray()
 */

return array(

    // database
    'database_connection_name' => 'mysql',
    'connection_mask_prefix' => 'railforums_',

    // tables
    'table_prefix' => 'forum_',
    'tables' => [
        'categories' => 'categories',
        'threads' => 'threads',
        'thread_follows' => 'thread_follows',
        'thread_reads' => 'thread_reads',
        'posts' => 'posts',
        'post_likes' => 'post_likes',
        'post_reports' => 'post_reports',
        'post_replies' => 'post_replies',
        'search_indexes' => 'search_indexes'
    ],

    // host does the db migrations, clients do not
    'data_mode' => 'host', // 'host' or 'client'

    // cache
    'cache_driver' => 'array',
    'cache_key_prefix' => 'railforums_cache_',
    'cache_minutes' => 60,

    // middleware
    'controller_middleware' => [
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    ],

    'author_table_name' => 'users',
    'author_table_id_column_name' => 'id',
    'author_table_display_name_column_name' => 'display_name',

    'user_data_mapper_class' => \Railroad\Railforums\DataMappers\UserCloakDataMapper::class,

    'post_report_notification_class' => \Railroad\Railforums\Notifications\PostReport::class,
    'post_report_notification_channel' => 'mail',
    'post_report_notification_recipients' => ['example@recordeo.com', 'example.two@recordeo.com'],
    'post_report_notification_view_post_route' => 'railforums.api.post.show', // laravel route name, eg: 'railforums.api.post.show' or 'forums.post.jump-to'

    'html_purifier_settings' => [
        'encoding' => 'UTF-8',
        'finalize' => true,
        'settings' => [
            'default' => [
                'HTML.Doctype' => 'XHTML 1.0 Strict',
                'HTML.Allowed' => 'div,b,strong,i,em,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]',
                'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align',
                'AutoFormat.AutoParagraph' => true,
                'AutoFormat.RemoveEmpty' => true,
            ],
        ],
    ],

    'search' => [
        'high_value_multiplier' => 4,
        'medium_value_multiplier' => 2,
        'low_value_multiplier' => 1,
    ],

    'role_abilities' => [
        'administrator' => [
            'update-threads' => [
                'except' => [
                    'id'
                ]
            ],
            'update-posts' => [
                'except' => [
                    'id'
                ]
            ]
        ],
        'moderator' => [
            'update-threads' => [
                'except' => [
                    'id'
                ]
            ],
            'update-posts' => [
                'except' => [
                    'id'
                ]
            ]
        ],
        'user' => [
            'create-posts',
            'like-posts',
            'read-threads',
            'create-threads',
            'follow-threads'
        ]
    ]
);
