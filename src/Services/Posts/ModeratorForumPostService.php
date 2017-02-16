<?php

namespace Railroad\Railforums\Services\Posts;

use Carbon\Carbon;
use Railroad\Railforums\Entities\Post;

class ModeratorForumPostService extends UserForumPostService
{
    protected $accessibleStates = [Post::STATE_PUBLISHED, Post::STATE_HIDDEN];

    /**
     * @param $id
     * @return bool
     */
    public function setPostAsPublished($id)
    {
        $post = $this->postDataMapper->get($id);

        if (!empty($post)) {
            $post->setState(Post::STATE_PUBLISHED);
            $post->persist();

            $this->postDataMapper->flushCache();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function setPostAsHidden($id)
    {
        $post = $this->postDataMapper->get($id);

        if (!empty($post)) {
            $post->setState(Post::STATE_HIDDEN);
            $post->persist();

            $this->postDataMapper->flushCache();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function destroyPost($id)
    {
        $post = $this->postDataMapper->get($id);

        if (!empty($post)) {
            $post->destroy();

            $this->postDataMapper->flushCache();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @param $content
     * @return bool
     * @internal param string $title
     */
    public function updatePostContent($id, $content)
    {
        $post = $this->postDataMapper->get($id);

        if (!empty($post)) {
            $post->setContent($this->htmlPurifierService->clean($content));
            $post->setEditedOn(Carbon::now()->toDateTimeString());
            $post->persist();

            return true;
        }

        return false;
    }
}