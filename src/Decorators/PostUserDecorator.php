<?php

namespace Railroad\Railforums\Decorators;

use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Railroad\Railforums\Contracts\UserProviderInterface;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Resora\Collections\BaseCollection;
use Railroad\Resora\Decorators\DecoratorInterface;

class PostUserDecorator extends ModeDecoratorBase implements DecoratorInterface
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
        if ($posts->isEmpty()) {
            return $posts;
        }

        $userIds =
            $posts->pluck('author_id')
                ->toArray();

        $userIds = array_unique($userIds);
        $users = $this->userProvider->getUsersByIds($userIds);
        $currentUser = $this->userProvider->getUser(auth()->id());

        if (self::$decorationMode !== self::DECORATION_MODE_MAXIMUM) {
            foreach ($posts as $postIndex => $post) {
                $posts[$postIndex]['published_on_formatted'] =
                    Carbon::parse($post['published_on'])
                        ->timezone($currentUser?->getTimezone() ?? 'America/Los_Angeles')
                        ->format('M j, Y') .
                    ' AT ' .
                    Carbon::parse($post['published_on'])
                        ->timezone($currentUser?->getTimezone() ?? 'America/Los_Angeles')
                        ->format('g:i A');
                $posts[$postIndex]['published_on_formatted'] =  $posts[$postIndex]['created_at_diff'] = Carbon::parse($posts[$postIndex]['created_at'])
                                        ->diffForHumans();

                $posts[$postIndex]['is_liked_by_viewer'] =
                    isset($post['is_liked_by_viewer']) && $post['is_liked_by_viewer'] == 1;

                if (!empty($users[$post['author_id']])) {
                    $user = $users[$post['author_id']];
                    $posts[$postIndex]['author']['id'] = $post['author_id'];
                    $posts[$postIndex]['author']['display_name'] = $user->getDisplayName();
                    $posts[$postIndex]['author']['avatar_url'] =
                        $user->getProfilePictureUrl() ?? config('railforums.author_default_avatar_url');
                    $posts[$postIndex]['author']['created_at'] =
                        $user->getCreatedAt()
                            ->toDateTimeString();
                    $posts[$postIndex]['author']['is_reported_by_viewer'] = $user->getIsReported();
                }
            }
            return $posts;
        }

        $signatures =
            $this->databaseManager->connection(config('railforums.database_connection'))
                ->table(ConfigService::$tableUserSignatures)
                ->select('user_id', 'signature')
                ->whereIn(ConfigService::$tableUserSignatures . '.user_id', $userIds)
                ->where('brand', config('railforums.brand'))
                ->get()
                ->toArray();

        $userSignatures = array_combine(array_column($signatures, 'user_id'), array_column($signatures, 'signature'));

        $postLikes =
            $this->databaseManager->connection(config('railforums.database_connection'))
                ->table(ConfigService::$tablePostLikes)
                ->selectRaw('COUNT(' . ConfigService::$tablePostLikes . '.id) as count')
                ->addSelect('liker_id')
                ->whereIn(ConfigService::$tablePostLikes . '.liker_id', $userIds)
                ->groupBy(ConfigService::$tablePostLikes . '.liker_id')
                ->get()
                ->toArray();

        $userLikes = array_combine(array_column($postLikes, 'liker_id'), array_column($postLikes, 'count'));

        $associatedCoaches = $this->userProvider->getAssociatedCoaches($userIds);

        $postsCount =
            $this->databaseManager->connection(config('railforums.database_connection'))
                ->table(ConfigService::$tablePosts)
                ->selectRaw('author_id, COUNT(' . ConfigService::$tablePosts . '.id) as count')
                ->whereIn('author_id', $userIds)
                ->groupBy('author_id')
                ->get()
                ->toArray();

        $userPosts = array_combine(array_column($postsCount, 'author_id'), array_column($postsCount, 'count'));

        foreach ($posts as $postIndex => $post) {
            $posts[$postIndex]['created_at_diff'] = Carbon::parse($posts[$postIndex]['created_at'])
                ->diffForHumans();

            $posts[$postIndex]['published_on_formatted'] =
                Carbon::parse($post['published_on'])
                    ->timezone($currentUser?->getTimezone() ?? 'America/Los_Angeles')
                    ->format('M j, Y') .
                ' AT ' .
                Carbon::parse($post['published_on'])
                    ->timezone($currentUser?->getTimezone() ?? 'America/Los_Angeles')
                    ->format('g:i A');

            $posts[$postIndex]['is_liked_by_viewer'] =
                isset($post['is_liked_by_viewer']) && $post['is_liked_by_viewer'] == 1;

            if (!empty($users[$post['author_id']])) {
                $user = $users[$post['author_id']];
                $posts[$postIndex]['author']['id'] = $post['author_id'];
                $posts[$postIndex]['author']['display_name'] = $user->getDisplayName();
                $posts[$postIndex]['author']['avatar_url'] =
                    $user->getProfilePictureUrl() ?? config('railforums.author_default_avatar_url');
                $posts[$postIndex]['author']['total_posts'] = $userPosts[$post['author_id']] ?? 0;
                $posts[$postIndex]['author']['days_as_member'] =
                    Carbon::parse($user->getCreatedAt())
                        ->diffInDays(Carbon::now());
                $posts[$postIndex]['author']['signature'] = $userSignatures[$post['author_id']] ?? null;
                $posts[$postIndex]['author']['access_level'] = $user->getAccessLevel();
                $posts[$postIndex]['author']['xp'] = $user->getXp();
                $posts[$postIndex]['author']['xp_rank'] = $user->getXpRank();
                $posts[$postIndex]['author']['total_post_likes'] = $userLikes[$post['author_id']] ?? 0;
                $posts[$postIndex]['author']['created_at'] =
                    $user->getCreatedAt()
                        ->toDateTimeString();
                $posts[$postIndex]['author']['level_rank'] = $user->getLevelRank() ?? '1.1';
                $posts[$postIndex]['author']['associated_coach'] =
                    array_key_exists($post['author_id'], $associatedCoaches) ? $associatedCoaches[$post['author_id']] :
                        null;
                $posts[$postIndex]['author']['is_reported_by_viewer'] = $user->getIsReported();
            }
        }

        return $posts;
    }
}
