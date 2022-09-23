<?php

namespace Railroad\Railforums\Decorators;

use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Railroad\Railforums\Contracts\UserProviderInterface;
use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Resora\Decorators\DecoratorInterface;

class DiscussionDecorator implements DecoratorInterface
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    private $postRepository;

    public function __construct(DatabaseManager $databaseManager, UserProviderInterface $userProvider, PostRepository $postRepository)
    {
        $this->databaseManager = $databaseManager;
        $this->userProvider = $userProvider;
        $this->postRepository = $postRepository;
    }

    /**
     * @param $discussions
     * @return mixed
     */
    public function decorate($discussions)
    {
        $postsIds =  $discussions->pluck('last_post_id')
            ->toArray();

        $posts = $this->postRepository->getDecoratedPostsByIds($postsIds)->keyBy('id');

        foreach ($discussions as $discussion) {
            $discussion['mobile_app_url'] = url()->route('railforums.mobile-app.show.discussion', [$discussion['id'], 'brand' => config('railforums.brand')]);
            $discussion['icon_path'] = config('railforums.icons.'.$discussion['slug']);
            $latestPosts = $discussion['last_post_id'];
            if ($latestPosts && $posts[$latestPosts]) {
                $user = $this->userProvider->getUser($posts[$latestPosts]->author_id);

                $discussion['latest_post']['id'] = $latestPosts;
                $discussion['latest_post']['created_at'] = $posts[$latestPosts]->created_at;
                $discussion['latest_post']['created_at_diff'] = Carbon::parse($posts[$latestPosts]->created_at)->diffForHumans();

                $discussion['latest_post']['thread_title'] = 'fffff'; //TODO
                $discussion['latest_post']['author_id'] = $posts[$latestPosts]->author_id;

                $discussion['latest_post']['author_display_name'] = $user->getDisplayName();
                $discussion['latest_post']['author_avatar_url'] =
                    $user->getProfilePictureUrl() ?? config('railforums.author_default_avatar_url');
            }
        }

        return $discussions;
    }
}
