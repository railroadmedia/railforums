<?php

namespace Railroad\Railforums\Decorators;

use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Railroad\Railforums\Contracts\UserProviderInterface;
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

    public function __construct(DatabaseManager $databaseManager, UserProviderInterface $userProvider)
    {
        $this->databaseManager = $databaseManager;
        $this->userProvider = $userProvider;
    }

    /**
     * @param $discussions
     * @return mixed
     */
    public function decorate($discussions)
    {
        foreach ($discussions as $discussion) {
            $discussion['mobile_app_url'] = url()->route('railforums.mobile-app.show.discussion', [$discussion['id']]);
            $discussion['icon_path'] = config('railforums.icons.'.$discussion['slug']);
            $posts =
                $this->databaseManager->connection(config('railforums.database_connection'))
                    ->table(ConfigService::$tablePosts . ' as p')
                    ->join(ConfigService::$tableThreads . ' as t', 't.id', '=', 'p.thread_id')
                    ->selectRaw(
                        'COUNT(*) as post_count'
                    )
                    ->whereNull('p.deleted_at')
                    ->whereNull('t.deleted_at')
                    ->where('t.category_id', $discussion['id'])->first();

            $discussion['post_count'] = $posts->post_count;

            $latestPosts =
                $this->databaseManager->connection(config('railforums.database_connection'))
                    ->table(ConfigService::$tablePosts . ' as p')
                    ->join(ConfigService::$tableThreads . ' as t', 't.id', '=', 'p.thread_id')
                    ->select(
                        'p.id as post_id',
                        't.*',
                        'p.content',
                        'p.author_id',
                        'p.published_on as last_post_created_at'
                    )
                    ->whereNull('p.deleted_at')
                    ->whereNull('t.deleted_at')
                    ->where('t.category_id', $discussion['id'])
                    ->orderBy('p.published_on', 'desc')
                    ->limit(1)
                    ->first();

            if ($latestPosts) {
                $user = $this->userProvider->getUser($latestPosts->author_id);

                $discussion['latest_post']['id'] = $latestPosts->post_id;
                $discussion['latest_post']['created_at'] = $latestPosts->last_post_created_at;

                $discussion['latest_post']['created_at_diff'] = Carbon::parse($latestPosts->last_post_created_at)->diffForHumans();


                $discussion['latest_post']['thread_title'] = $latestPosts->title;
                $discussion['latest_post']['author_id'] = $latestPosts->author_id;

                $discussion['latest_post']['author_display_name'] = $user->getDisplayName();
                $discussion['latest_post']['author_avatar_url'] =
                    $user->getProfilePictureUrl() ?? config('railforums.author_default_avatar_url');
            }
        }

        return $discussions;
    }
}
