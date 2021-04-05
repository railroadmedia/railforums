<?php

namespace Railroad\Railforums\Decorators;

use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\DB;
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
     * @param BaseCollection $threads
     * @return BaseCollection
     */
    public function decorate($discussions)
    {
        foreach ($discussions as $discussion) {
            $latestPosts = $this->databaseManager->connection(config('railforums.database_connection'))
                ->table(ConfigService::$tablePosts)
                ->select(ConfigService::$tablePosts.'.id','thread_id', 'author_id', DB::raw('MAX(updated_at) as last_post_created_at'))
                ->whereNull('deleted_at')
                ->groupBy('thread_id');

            $threads =
                $this->databaseManager->connection(config('railforums.database_connection'))
                    ->table(ConfigService::$tableCategories)
                    ->select(
                        [
                            ConfigService::$tableCategories.'.id as category_id',
                            'threads.*',
                            'latest_posts.id as post_id',
                            'latest_posts.last_post_created_at as last_post_created_at',
                            'latest_posts.author_id as latest_post_author_id',
                        ]
                    )
                    ->leftJoin(
                        ConfigService::$tableThreads . ' as threads',
                        ConfigService::$tableCategories . '.id',
                        '=',
                         'threads.category_id'
                    )
                    ->leftJoinSub($latestPosts, 'latest_posts', function ($join) {
                        $join->on( 'threads.id', '=', 'latest_posts.thread_id');
                    })
                    ->where(ConfigService::$tableCategories . '.id', $discussion['id'])
                    ->get();

            $userIds = array_unique($threads->pluck('author_id')
                ->toArray());

            $users =
                $this->databaseManager->connection(config('railforums.author_database_connection'))
                    ->table(config('railforums.author_table_name'))
                    ->select(
                        [
                            config('railforums.author_table_id_column_name'),
                            config('railforums.author_table_display_name_column_name'),
                            config('railforums.author_table_avatar_column_name'),
                        ]
                    )
                    ->whereIn(config('railforums.author_table_id_column_name'), $userIds)
                    ->get()
                    ->keyBy(config('railforums.author_table_id_column_name'));

            $displayNameColumnName = config('railforums.author_table_display_name_column_name');
            $avatarUrlColumnName = config('railforums.author_table_avatar_column_name');

             foreach ($threads as $thread){
                if($thread->post_id){
                    $discussion['latest_post']['id'] = $thread->post_id;
                    $discussion['latest_post']['created_at'] = $thread->last_post_created_at;
                    $discussion['latest_post']['thread_title'] = $thread->title;
                    $discussion['latest_post']['author_id'] = $thread->latest_post_author_id;
                    if (!empty($users[$thread->latest_post_author_id])) {
                        $user = $users[$thread->latest_post_author_id];
                        $discussion['latest_post']['author_display_name'] = $user->$displayNameColumnName;
                        $discussion['latest_post']['author_avatar_url'] =
                            $user->$avatarUrlColumnName ?? config('railforums.author_default_avatar_url');
                    }
                }
            }
        }

        return $discussions;
    }
}
