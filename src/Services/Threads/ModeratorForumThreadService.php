<?php

namespace Railroad\Railforums\Services\Threads;

use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railmap\Helpers\RailmapHelpers;

class ModeratorForumThreadService extends UserForumThreadService
{
    protected $accessibleStates = [Post::STATE_PUBLISHED, Post::STATE_HIDDEN];

    /**
     * @param $id
     * @return bool
     */
    public function setThreadAsPublished($id)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread)) {
            $thread->setState(Thread::STATE_PUBLISHED);
            $thread->persist();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function setThreadAsHidden($id)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread)) {
            $thread->setState(Thread::STATE_HIDDEN);
            $thread->persist();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @param bool $state
     * @return bool
     */
    public function setThreadLocked($id, bool $state)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread)) {
            $thread->setLocked($state);
            $thread->persist();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @param bool $state
     * @return bool
     */
    public function setThreadPinned($id, bool $state)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread)) {
            $thread->setPinned($state);
            $thread->persist();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function destroyThread($id)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread)) {
            $thread->destroy();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @param string $title
     * @return bool
     */
    public function updateThreadTitle($id, $title)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread)) {
            $thread->setTitle($title);
            $thread->setSlug(RailmapHelpers::sanitizeForSlug($title));
            $thread->persist();

            return true;
        }

        return false;
    }
}