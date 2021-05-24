<?php

namespace Railroad\Railforums\Decorators;

use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Railroad\Railforums\Contracts\UserProviderInterface;
use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Resora\Collections\BaseCollection;
use Railroad\Resora\Decorators\DecoratorInterface;

class ThreadUserDecorator implements DecoratorInterface
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
     * @var PostRepository
     */
    private $postRepository;

    public function __construct(
        DatabaseManager $databaseManager,
        UserProviderInterface $userProvider,
        PostRepository $postRepository
    ) {
        $this->databaseManager = $databaseManager;
        $this->userProvider = $userProvider;
        $this->postRepository = $postRepository;
    }

    /**
     * @param BaseCollection $threads
     * @return BaseCollection
     */
    public function decorate($threads)
    {
        $lastPostIds =
            array_unique(
                $threads->pluck('last_post_id')
                    ->toArray()
            );
        $lastPosts =
            $this->postRepository->getDecoratedPostsByIds($lastPostIds)
                ->keyBy('id');

        $userIds = array_merge(
            $threads->pluck('author_id')
                ->toArray(),
            $lastPosts->pluck('author_id')
                ->toArray()
        );

        $userIds = array_unique($userIds);

        $users = $this->userProvider->getUsersByIds($userIds);

        foreach ($threads as $threadIndex => $thread) {

            $threads[$threadIndex]['mobile_app_url'] =
                url()->route('railforums.mobile-app.show.thread', [$thread['id']]);

            $threads[$threadIndex]['author_display_name'] =
                (isset($users[$thread['author_id']])) ? $users[$thread['author_id']]->getDisplayName() : '';

            $threads[$threadIndex]['author_avatar_url'] =
                $users[$thread['author_id']]->getProfilePictureUrl() ?? config('railforums.author_default_avatar_url');

            $threads[$threadIndex]['author_access_level'] =
                $this->userProvider->getUserAccessLevel($thread['author_id']);
            $threads[$threadIndex]['published_on_formatted'] =
                Carbon::parse($thread['published_on'])
                    ->format('M d, Y');

            if (array_key_exists('last_post_id', $thread)) {
                $lastPost = $lastPosts[$thread['last_post_id']];
                $threads[$threadIndex]['latest_post']['id'] = $lastPost['id'];
                $threads[$threadIndex]['latest_post']['created_at'] = $lastPost['published_on'];
                $threads[$threadIndex]['latest_post']['created_at_diff'] =
                    Carbon::parse($lastPost['published_on'])
                        ->diffForHumans();

                $threads[$threadIndex]['latest_post']['author_id'] = $lastPost['author_id'];
                $threads[$threadIndex]['latest_post']['author_display_name'] =
                    $users[$lastPost['author_id']]->getDisplayName();
                $threads[$threadIndex]['latest_post']['author_avatar_url'] =
                    $users[$lastPost['author_id']]->getProfilePictureUrl()
                    ??
                    config('railforums.author_default_avatar_url');
            }
        }

        return $threads;
    }
}
