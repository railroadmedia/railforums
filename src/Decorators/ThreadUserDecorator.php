<?php

namespace Railroad\Railforums\Decorators;

use Illuminate\Database\DatabaseManager;
use Railroad\Resora\Collections\BaseCollection;
use Railroad\Resora\Decorators\DecoratorInterface;

class ThreadUserDecorator implements DecoratorInterface
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
    public function decorate($threads)
    {
        $userIds = array_merge(
            $threads->pluck('author_id')
                ->toArray(),
            $threads->pluck('last_post_user_id')
                ->toArray()
        );

        $userIds = array_unique($userIds);

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

        foreach ($threads as $threadIndex => $thread) {
            if (!empty($users[$thread['author_id']])) {
                $user = $users[$thread['author_id']];
                $threads[$threadIndex]['last_post_user_display_name'] = $user->$displayNameColumnName;
                $threads[$threadIndex]['last_post_user_avatar_url'] =
                    $user->$avatarUrlColumnName ?? config('railforums.author_default_avatar_url');
            }
        }

        return $threads;
    }
}
