<?php

namespace Railroad\Railforums\EventListeners;

use Carbon\Carbon;
use Railroad\Railforums\DataMappers\PostDataMapper;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\PostLike;
use Railroad\Railforums\Entities\ThreadFollow;
use Railroad\Railforums\Entities\ThreadRead;
use Railroad\Railmap\Events\EntityDestroyed;
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
            $thread = $this->threadDataMapper->ignoreCache()->get($event->newEntity->getThreadId());
            $dataMapper = $event->newEntity->getOwningDataMapper();

            $thread->setPostCount(
                $dataMapper->countPostsInThread($event->newEntity->getThreadId())
            );

            if (empty($thread->getLastPost()) ||
                Carbon::parse($thread->getLastPost()->getPublishedOn()) <=
                Carbon::parse($event->newEntity->getPublishedOn()) &&
                $event->newEntity->getState() == Post::STATE_PUBLISHED
            ) {
                $thread->setLastPostId($event->newEntity->getId());
                $thread->setLastPost($event->newEntity);

            }
            $thread->persist();
        }

        if ($event->newEntity instanceof PostLike) {
            $post = $this->postDataMapper->ignoreCache()->get($event->newEntity->getPostId());
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
            $thread = $this->threadDataMapper->ignoreCache()->get($event->newEntity->getThreadId());
            $thread->setIsRead($event->newEntity->getReadOn() >= $thread->getLastPost()->getPublishedOn());
            $thread->persist();
        }

        if ($event->newEntity instanceof ThreadFollow) {
            $thread = $this->threadDataMapper->ignoreCache()->get($event->newEntity->getThreadId());
            $thread->setIsFollowed(true);
            $thread->persist();
        }
    }

    public function onDestroyed(EntityDestroyed $event)
    {
        if ($event->entity instanceof PostLike) {
            $post = $this->postDataMapper->ignoreCache()->get($event->entity->getPostId());
            $dataMapper = $event->entity->getOwningDataMapper();

            if (!empty($post)) {
                $post->setLikeCount(
                    $dataMapper->ignoreCache()->countPostLikes($event->entity->getPostId())
                );
                $post->persist();
            }
        }

        if ($event->entity instanceof Post) {
            $thread = $this->threadDataMapper->ignoreCache()->get($event->entity->getThreadId());
            $dataMapper = $event->entity->getOwningDataMapper();

            $postCount = $dataMapper->ignoreCache()->countPostsInThread($event->entity->getThreadId());

            if ($postCount) {
                $thread->setPostCount($postCount);

                $latestPost = $dataMapper->ignoreCache()->getLatestPost($thread->getId());

                $thread->setLastPostId($latestPost->getId());
                $thread->setLastPost($latestPost);

                $thread->persist();

            } else {
                // if last and only post of this thread is deleted, thread data becomes inconsistent
                $thread->destroy();
                $this->threadDataMapper->flushCache();
            }
        }

        if ($event->entity instanceof ThreadFollow) {
            $thread = $this->threadDataMapper->ignoreCache()->get($event->entity->getThreadId());
            $thread->setIsFollowed(false);
            $thread->persist();
        }
    }
}