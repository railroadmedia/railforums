<?php

namespace Railroad\Railforums\EventListeners;

use Carbon\Carbon;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\ThreadRead;
use Railroad\Railmap\Events\EntitySaved;

class EntityEventListener
{
    private $threadDataMapper;

    public function __construct(ThreadDataMapper $threadDataMapper)
    {
        $this->threadDataMapper = $threadDataMapper;
    }

    public function onSaved(EntitySaved $event)
    {
        if ($event->newEntity instanceof Post) {
            $thread = $this->threadDataMapper->get($event->newEntity->getThreadId());

            if (empty($thread->getLastPost()) ||
                Carbon::parse($thread->getLastPost()->getPublishedOn()) <=
                Carbon::parse($event->newEntity->getPublishedOn())
            ) {
                $thread->setLastPostId($event->newEntity->getId());
                $thread->setPostCount($thread->getPostCount() + 1);
                $thread->setLastPost($event->newEntity);
                $thread->persist();
            }
        }

        if ($event->newEntity instanceof ThreadRead) {
            $thread = $this->threadDataMapper->get($event->newEntity->getThreadId());
            $thread->setIsRead($event->newEntity->getReadOn() >= $thread->getLastPost()->getPublishedOn());
            $thread->persist();
        }
    }
}