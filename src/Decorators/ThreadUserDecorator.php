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
    )
    {
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
        $lastPostIds = array_unique(
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

            $threads[$threadIndex]['locked'] = $thread['locked'] == 1;
            $threads[$threadIndex]['pinned'] = $thread['pinned'] == 1;
            $threads[$threadIndex]['is_followed'] = isset($thread['is_followed']) && $thread['is_followed'] == 1;


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

            if (array_key_exists('last_post_id', $thread) && $thread['last_post_id'] != 0) {
                $lastPost = $lastPosts[$thread['last_post_id']];
                $threads[$threadIndex]['latest_post']['id'] = $lastPost['id'];
                $threads[$threadIndex]['latest_post']['created_at'] = $lastPost['published_on'];
                $threads[$threadIndex]['latest_post']['created_at_diff'] =
                    str_replace(['mo', 'mos'], ['M', 'M'], Carbon::parse($lastPost['published_on'])
                        ->diffForHumans(null, null, true));

                $threads[$threadIndex]['latest_post']['author_id'] = $lastPost['author_id'];
                $threads[$threadIndex]['latest_post']['author_display_name'] = '';
                $threads[$threadIndex]['latest_post']['author_avatar_url'] = config('railforums.author_default_avatar_url');

                if (array_key_exists($lastPost['author_id'], $users)) {
                    $threads[$threadIndex]['latest_post']['author_display_name'] =
                        $users[$lastPost['author_id']]->getDisplayName();
                    $threads[$threadIndex]['latest_post']['author_avatar_url'] =
                        $users[$lastPost['author_id']]->getProfilePictureUrl();
                }

                if (Carbon::parse($lastPost['published_on'])->greaterThanOrEqualTo(Carbon::now()->subDays(3))) {
                    $threads[$threadIndex]['is_read'] = isset($thread['is_read']) && $thread['is_read'] == 1;
                } else {
                    $threads[$threadIndex]['is_read'] = true;
                }
            }
        }

        return $threads;
    }
}
