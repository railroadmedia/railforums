<?php

namespace Railroad\Railforums\Services\Posts;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Events\PostDeleted;
use Railroad\Railforums\Events\PostUpdated;
use Railroad\Railforums\Exceptions\CannotDeleteFirstPostInThread;

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
     * @throws CannotDeleteFirstPostInThread
     */
    public function destroyPost($id)
    {
        $post = $this->postDataMapper->get($id);

        if ($this->postFirstInThread($id)) {
            throw new CannotDeleteFirstPostInThread($id);
        }

        if (!empty($post)) {
            $post->destroy();

            $this->postDataMapper->flushCache();

            event(new PostDeleted($id, $this->userCloakDataMapper->getCurrentId()));

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

            event(new PostUpdated($id, $this->userCloakDataMapper->getCurrentId()));

            return true;
        }

        return false;
    }

    /**
     * @param $threadId
     * @return array
     */
    public function getAllPostIdsInThread($threadId)
    {
        return $this->postDataMapper->list(
            function (Builder $builder) use ($threadId) {
                return $builder->where('forum_posts.thread_id', $threadId)->orderBy('published_on');
            },
            'id'
        );
    }
}