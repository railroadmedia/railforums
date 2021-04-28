<?php

namespace Railroad\Railforums\Decorators;

use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Railroad\Railforums\Contracts\UserProviderInterface;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Resora\Collections\BaseCollection;
use Railroad\Resora\Decorators\DecoratorInterface;

class PostUserDecorator implements DecoratorInterface
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * PostUserDecorator constructor.
     *
     * @param DatabaseManager $databaseManager
     * @param UserProviderInterface $userProvider
     */
    public function __construct(
        DatabaseManager $databaseManager,
        UserProviderInterface $userProvider
    ) {
        $this->databaseManager = $databaseManager;
        $this->userProvider = $userProvider;
    }

    /**
     * @param BaseCollection $posts
     * @return BaseCollection
     */
    public function decorate($posts)
    {
        $userIds =
            $posts->pluck('author_id')
                ->toArray();

        $userIds = array_unique($userIds);

        $postsCount =
            $this->databaseManager->connection(config('railforums.database_connection'))
                ->table(ConfigService::$tablePosts)
                ->selectRaw('author_id, COUNT(' . ConfigService::$tablePosts . '.id) as count')
                ->whereIn('author_id', $userIds)
                ->groupBy('author_id')
                ->get()
                ->toArray();

        $userPosts = array_combine(array_column($postsCount, 'author_id'), array_column($postsCount, 'count'));

        $users = $this->userProvider->getUsersByIds($userIds);

        $usersAccessLevel = $this->userProvider->getUsersAccessLevel($userIds);

        $usersXp = $this->userProvider->getUsersXPAndRank($userIds);

        $signatures =
            $this->databaseManager->connection(config('railforums.database_connection'))
                ->table(ConfigService::$tableUserSignatures)
                ->select('user_id', 'signature')
                ->whereIn(ConfigService::$tableUserSignatures . '.user_id', $userIds)
                ->where('brand', config('railforums.brand'))
                ->groupBy('user_id')
                ->get()
                ->toArray();

        $userSignatures = array_combine(array_column($signatures, 'user_id'), array_column($signatures, 'signature'));

        foreach ($posts as $postIndex => $post) {

            if (!empty($users[$post['author_id']])) {

                $user = $users[$post['author_id']];

                $posts[$postIndex]['author_display_name'] = $user->getDisplayName();
                $posts[$postIndex]['author_avatar_url'] =
                    $user->getProfilePictureUrl() ?? config('railforums.author_default_avatar_url');
                $posts[$postIndex]['author_total_posts'] = $userPosts[$post['author_id']] ?? 0;
                $posts[$postIndex]['author_days_as_member'] =
                    Carbon::parse($user->getCreatedAt())
                        ->diffInDays(Carbon::now());
                $posts[$postIndex]['author_signature'] = $userSignatures[$post['author_id']] ?? null;
                $posts[$postIndex]['author_access_level'] = $usersAccessLevel[$post['author_id']] ?? null;
                $posts[$postIndex]['author_xp'] =
                    (array_key_exists($post['author_id'], $usersXp)) ? $usersXp[$post['author_id']]['xp'] : 0;
                $posts[$postIndex]['author_xp_rank'] =
                    (array_key_exists($post['author_id'], $usersXp)) ? $usersXp[$post['author_id']]['xp_rank'] :
                        'Pianote Member';
            }
        }

        return $posts;
    }
}
