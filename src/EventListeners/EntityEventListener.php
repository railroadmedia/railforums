<?php

namespace Railroad\Railforums\EventListeners;

use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railforums\Entities\Post;
use Railroad\Railmap\Events\EntityCreated;

class EntityEventListener
{
    private $threadDataMapper;

    public function __construct(ThreadDataMapper $threadDataMapper)
    {
        $this->threadDataMapper = $threadDataMapper;
    }

    public function onCreated(EntityCreated $event)
    {
        if ($event->entity instanceof Post) {
            $thread = $this->threadDataMapper->get($event->entity->getThreadId());
            $thread->setLastPostId($event->entity->getId());
            $thread->setPostCount($thread->getPostCount() + 1);
            $thread->persist();
        }
    }
}