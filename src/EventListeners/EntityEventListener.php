<?php

namespace Railroad\Railforums\EventListeners;

use Carbon\Carbon;
use Railroad\Railforums\DataMappers\PostDataMapper;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\PostLike;
use Railroad\Railforums\Entities\ThreadRead;
use Railroad\Railmap\Events\EntitySaved;

class EntityEventListener
{
    private $threadDataMapper;
    private $postDataMapper;

    public function __construct(ThreadDataMapper $threadDataMapper, PostDataMapper $postDataMapper)
    {
        $this->threadDataMapper = $threadDataMapper;
        $this->postDataMapper = $postDataMapper;
    }

    public function onSaved(EntitySaved $event)
    {
        if ($event->newEntity instanceof Post) {
            $thread = $this->threadDataMapper->get($event->newEntity->getThreadId());

            if (empty($thread->getLastPost()) ||
                Carbon::parse($thread->getLastPost()->getPublishedOn()) <=
                Carbon::parse($event->newEntity->getPublishedOn()) &&
                $event->newEntity->getState() == Post::STATE_PUBLISHED
            ) {
                $dataMapper = $event->newEntity->getOwningDataMapper();

                $thread->setLastPostId($event->newEntity->getId());
                $thread->setLastPost($event->newEntity);
                $thread->setPostCount(
                    $dataMapper->countPostsInThread($event->newEntity->getThreadId())
                );
                $thread->persist();
            }
        }

        if ($event->newEntity instanceof PostLike) {
            $post = $this->postDataMapper->get($event->newEntity->getPostId());
            $dataMapper = $event->newEntity->getOwningDataMapper();

            if (!empty($post)) {
                $post->setLikeCount(
                    $dataMapper->countPostLikes($event->newEntity->getPostId())
                );
                $post->persist();
            }

            if (!empty($event->oldEntity) &&
                $event->oldEntity instanceof PostLike &&
                $event->newEntity->getId() != $event->oldEntity->getId()
            ) {
                $oldLikePost = $this->postDataMapper->get($event->oldEntity->getPostId());

                $oldLikePost->setLikeCount(
                    $dataMapper->countPostLikes($event->newEntity->getPostId())
                );
                $oldLikePost->persist();
            }
        }

        if ($event->newEntity instanceof ThreadRead) {
            $thread = $this->threadDataMapper->get($event->newEntity->getThreadId());
            $thread->setIsRead($event->newEntity->getReadOn() >= $thread->getLastPost()->getPublishedOn());
            $thread->persist();
        }
    }
}