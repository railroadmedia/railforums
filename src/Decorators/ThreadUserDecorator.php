<?php

namespace Railroad\Railforums\Decorators;

use Illuminate\Database\DatabaseManager;
use Railroad\Railforums\Contracts\UserProviderInterface;
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

    public function __construct(DatabaseManager $databaseManager, UserProviderInterface $userProvider)
    {
        $this->databaseManager = $databaseManager;
        $this->userProvider = $userProvider;
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

        $users = $this->userProvider->getUsersByIds($userIds);

        foreach ($threads as $threadIndex => $thread) {
            
            $threads[$threadIndex]['mobile_app_url'] =  url()->route('railforums.mobile-app.show.thread', [$thread['id']]);

            $threads[$threadIndex]['author_display_name'] =
                (isset($users[$thread['author_id']])) ? $users[$thread['author_id']]->getDisplayName() : '';

            if (!empty($thread['last_post_user_id']) && (!empty($users[$thread['last_post_user_id']]))) {

                $user = $users[$thread['last_post_user_id']];

                $threads[$threadIndex]['last_post_user_display_name'] = $user->getDisplayName();
                $threads[$threadIndex]['last_post_user_avatar_url'] =
                    $user->getProfilePictureUrl() ?? config('railforums.author_default_avatar_url');
                $threads[$threadIndex]['access_level'] =
                    $this->userProvider->getUserAccessLevel($thread['last_post_user_id']);
            }
        }

        return $threads;
    }
}
