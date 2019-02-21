<?php

namespace Railroad\Railforums\Decorators;

use Illuminate\Database\DatabaseManager;
use Railroad\Resora\Collections\BaseCollection;
use Railroad\Resora\Decorators\DecoratorInterface;

class PostUserDecorator implements DecoratorInterface
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
     * @param BaseCollection $posts
     * @return BaseCollection
     */
    public function decorate($posts)
    {
        $userIds = array_merge(
            $posts->pluck('author_id')
                ->toArray(),
            $posts->pluck('liker_1_id')
                ->toArray(),
            $posts->pluck('liker_2_id')
                ->toArray(),
            $posts->pluck('liker_3_id')
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

        foreach ($posts as $postIndex => $post) {

            if (!empty($users[$post['author_id']])) {
                $user = $users[$post['author_id']];
                $posts[$postIndex]['author_display_name'] = $user->$displayNameColumnName;
                $posts[$postIndex]['author_avatar_url'] = $user->$avatarUrlColumnName;
            }

            if (!empty($users[$post['liker_1_id'] ?? null])) {
                $user = $users[$post['liker_1_id']];
                $posts[$postIndex]['liker_1_display_name'] = $user->$displayNameColumnName;
                $posts[$postIndex]['liker_1_avatar_url'] = $user->$avatarUrlColumnName;
            }

            if (!empty($users[$post['liker_2_id'] ?? null])) {
                $user = $users[$post['liker_2_id']];
                $posts[$postIndex]['liker_2_display_name'] = $user->$displayNameColumnName;
                $posts[$postIndex]['liker_2_avatar_url'] = $user->$avatarUrlColumnName;
            }

            if (!empty($users[$post['liker_3_id'] ?? null])) {
                $user = $users[$post['liker_3_id']];
                $posts[$postIndex]['liker_3_display_name'] = $user->$displayNameColumnName;
                $posts[$postIndex]['liker_3_avatar_url'] = $user->$avatarUrlColumnName;
            }

        }

        return $posts;
    }
}
