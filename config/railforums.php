<?php

/*
 * html_purifier_settings.settings.default array is passed directly in to HTMLPurifier_Config->loadArray()
 */

return [

    // brand
    'brand' => 'brand',

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
        'search_indexes' => 'search_indexes',
    ],

    // host does the db migrations, clients do not
    'data_mode' => 'host',
    // 'host' or 'client'

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

    // author database info
    'author_database_connection' => 'mysql',
    'author_table_name' => 'users',
    'author_table_id_column_name' => 'id',
    'author_table_display_name_column_name' => 'display_name',
    'author_table_avatar_column_name' => 'avatar_url',
    'author_default_avatar_url' => 'https://s3.amazonaws.com/pianote/defaults/avatar.png',

    'user_data_mapper_class' => \Railroad\Railforums\DataMappers\UserCloakDataMapper::class,

    'post_report_notification_class' => \Railroad\Railforums\Notifications\PostReport::class,
    'post_report_notification_channel' => 'mail',
    'post_report_notification_recipients' => ['example@recordeo.com', 'example.two@recordeo.com'],
    'post_report_notification_view_post_route' => 'railforums.api.post.show',
    // laravel route name, eg: 'railforums.api.post.show' or 'forums.post.jump-to'

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

    // copy to permissions config file
    'role_abilities' => [
        'administrator' => [
            'like-posts',
            'index-posts',
            'show-posts',
            'create-posts',
            'update-posts' => ['only' => ['content', 'thread_id']],
            'delete-posts',
            'read-threads',
            'follow-threads',
            'create-threads',
            'update-threads' => ['only' => ['title', 'category_id']],
            'delete-threads',
            'report-posts',
        ],
        'moderator' => [
            'like-posts',
            'index-posts',
            'show-posts',
            'create-posts',
            'update-posts' => ['only' => ['content', 'thread_id']],
            'delete-posts',
            'read-threads',
            'follow-threads',
            'create-threads',
            'update-threads' => ['only' => ['title', 'category_id']],
            'delete-threads',
            'report-posts',
        ],
        'user' => [
            'like-posts',
            'index-posts',
            'show-posts',
            'create-posts',
            'read-threads',
            'follow-threads',
            'create-threads',
        ],
    ],
];
