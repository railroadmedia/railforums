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
        $postsIds =  $threads->pluck('last_post_id')
            ->toArray();

        $posts = $this->postRepository->getPostsByIds($postsIds);

        $userIds = array_merge(
            $threads->pluck('author_id')
                ->toArray(),
            $posts->pluck('author_id')
                ->toArray()
        );

        $userIds = array_unique($userIds);

        $users = $this->userProvider->getUsersByIds($userIds);



        foreach ($threads as $threadIndex => $thread) {

            $threads[$threadIndex]['locked'] = $thread['locked'] == 1;
            $threads[$threadIndex]['pinned'] = $threads[$threadIndex]['isPinned'] = $thread['pinned'] == 1;
            $threads[$threadIndex]['is_followed'] = isset($thread['is_followed']) && $thread['is_followed'] == 1;
            $threads[$threadIndex]['replyAmount'] = $thread['post_count'] ;

            $threads[$threadIndex]['mobile_app_url'] =
                url()->route('railforums.mobile-app.show.thread', [$thread['id'], 'brand'=> config('railforums.brand')]);

            $threads[$threadIndex]['author_display_name'] = $threads[$threadIndex]['authorUsername'] =
                (isset($users[$thread['author_id']])) ? $users[$thread['author_id']]->getDisplayName() : '';

            $threads[$threadIndex]['author_avatar_url'] = $threads[$threadIndex]['authorAvatar'] =
                (isset($users[$thread['author_id']]))?$users[$thread['author_id']]->getProfilePictureUrl() : config('railforums.author_default_avatar_url');

            $threads[$threadIndex]['author_access_level'] =
                (isset($users[$thread['author_id']]))?$users[$thread['author_id']]->getLevelRank():'pack';
            $threads[$threadIndex]['published_on_formatted'] =
                Carbon::parse($thread['published_on'])
                    ->format('M d, Y');
            $threads[$threadIndex]['createdOn'] =
                Carbon::parse($thread['published_on'])
                    ->diffforHumans();
            $threads[$threadIndex]['url'] = url()->route(
                'forums.show-thread-posts',
                [$thread['category_slug'] ?? '', $thread['category_id'], $thread['slug'], $thread['id']]
            );
            if (array_key_exists('last_post_id', $thread->getArrayCopy()) && $thread['last_post_id'] != 0) {
                $lastPost = $posts[$thread['last_post_id']];
                if ($lastPost) {
                    $threads[$threadIndex]['latest_post']['id'] = $thread['last_post_id'];
                    $threads[$threadIndex]['latest_post']['created_at'] = $lastPost->published_on;
                    $threads[$threadIndex]['latest_post']['created_at_diff'] =
                        str_replace(['mo', 'mos'], ['M', 'M'], Carbon::parse($lastPost->published_on)
                            ->diffForHumans(null, null, true));

                    $threads[$threadIndex]['latest_post']['author_id'] = $lastPost->author_id;
                    $threads[$threadIndex]['latest_post']['author_display_name'] = $users[$lastPost->author_id]->getDisplayName();
                    $threads[$threadIndex]['latest_post']['author_avatar_url'] = $users[$lastPost->author_id]->getProfilePictureUrl() ?? config('railforums.author_default_avatar_url');
                    $threads[$threadIndex]['latest_post']['url'] = url()->route('forums.jump-to-post', [$thread['last_post_id']]);
                    if (Carbon::parse($lastPost->published_on)->greaterThanOrEqualTo(Carbon::now()->subDays(3))) {
                        $threads[$threadIndex]['is_read'] = isset($thread['is_read']) && $thread['is_read'] == 1;
                    } else {
                        $threads[$threadIndex]['is_read'] = true;
                    }
                    $threads[$threadIndex]['latestPost'] =  $threads[$threadIndex]['latest_post'];
                }
            }
        }

        return $threads;
    }
}
