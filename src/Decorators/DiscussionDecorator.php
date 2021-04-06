<?php

namespace Railroad\Railforums\Decorators;

use Illuminate\Database\DatabaseManager;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Resora\Collections\BaseCollection;
use Railroad\Resora\Decorators\DecoratorInterface;

class DiscussionDecorator implements DecoratorInterface
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * @param $discussions
     * @return mixed
     */
    public function decorate($discussions)
    {
        foreach ($discussions as $discussion) {
            $posts =
                $this->databaseManager->connection(config('railforums.database_connection'))
                    ->table(ConfigService::$tablePosts . ' as p')
                    ->join(ConfigService::$tableThreads . ' as t', 't.id', '=', 'p.thread_id')
                    ->selectRaw(
                        'COUNT(*) as post_count'
                    )
                    ->whereNull('p.deleted_at')
                    ->where('t.category_id', $discussion['id'])
                    ->first();

            $discussion['post_count'] = $posts->post_count;
            $discussion['url'] = url()->route('forums.thread.list', [$discussion['slug'],$discussion['id'] ]);

            $latestPosts =
                $this->databaseManager->connection(config('railforums.database_connection'))
                    ->table(ConfigService::$tablePosts . ' as p')
                    ->join(ConfigService::$tableThreads . ' as t', 't.id', '=', 'p.thread_id')
                    ->select(
                        'p.id as post_id',
                        't.*',
                        'p.content',
                        'p.author_id',
                        'p.updated_at as last_post_created_at'
                    )
                    ->whereNull('p.deleted_at')
                    ->where('t.category_id', $discussion['id'])
                    ->orderBy('p.updated_at', 'desc')
                    ->limit(1)
                    ->first();

            if ($latestPosts) {
                $user =
                    $this->databaseManager->connection(config('railforums.author_database_connection'))
                        ->table(config('railforums.author_table_name'))
                        ->select(
                            [
                                config('railforums.author_table_id_column_name'),
                                config('railforums.author_table_display_name_column_name'),
                                config('railforums.author_table_avatar_column_name'),
                            ]
                        )
                        ->where(config('railforums.author_table_id_column_name'), $latestPosts->author_id)
                        ->first();

                $displayNameColumnName = config('railforums.author_table_display_name_column_name');
                $avatarUrlColumnName = config('railforums.author_table_avatar_column_name');

                $discussion['latest_post']['id'] = $latestPosts->post_id;
                $discussion['latest_post']['created_at'] = $latestPosts->last_post_created_at;

                $discussion['latest_post']['thread_title'] = $latestPosts->title;
                $discussion['latest_post']['author_id'] = $latestPosts->author_id;

                $discussion['latest_post']['author_display_name'] = $user->$displayNameColumnName;
                $discussion['latest_post']['author_avatar_url'] =
                    $user->$avatarUrlColumnName ?? config('railforums.author_default_avatar_url');
            }
        }

        return $discussions;
    }
}
