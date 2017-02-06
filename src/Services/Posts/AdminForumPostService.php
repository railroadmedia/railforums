<?php

namespace Railroad\Railforums\Services\Posts;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Railroad\Railforums\DataMappers\PostDataMapper;
use Railroad\Railforums\Entities\Post;

class ForumPostService
{
    private $htmlPurifierService;
    private $postDataMapper;

    public function __construct(
        HTMLPurifierService $htmlPurifierService,
        PostDataMapper $postDataMapper
    ) {
        $this->htmlPurifierService = $htmlPurifierService;
        $this->postDataMapper = $postDataMapper;
    }

    /**
     * @param $amount
     * @param $page
     * @param $threadId
     * @param array $states
     * @param string $sortColumn
     * @param string $sortDirection
     * @return Post|Post[]
     */
    public function getPostsSortedPaginated(
        $amount,
        $page,
        $threadId,
        $states = [Post::STATE_PUBLISHED],
        $sortColumn = 'published_on',
        $sortDirection = 'desc'
    ) {
        return $this->postDataMapper->getWithQuery(
            function (Builder $builder) use (
                $amount,
                $page,
                $sortColumn,
                $sortDirection,
                $states,
                $threadId
            ) {
                return $builder->limit($amount)->skip($amount * ($page - 1))->orderByRaw(
                    $sortColumn . ' ' . $sortDirection . ', id ' . $sortDirection
                )->where('thread_id', $threadId)->get();
            }
        );
    }

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

            return true;
        }

        return false;
    }

    /**
     * @param array $states
     * @param $threadId
     * @return int
     */
    public function getPostCount($threadId, $states = [Post::STATE_PUBLISHED])
    {
        return $this->postDataMapper->count(
            function (Builder $builder) use ($states, $threadId) {
                return $builder->whereIn('forum_posts.state', $states)->where('thread_id', $threadId);
            }
        );
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

    /**
     * @param string $content
     * @param int $promptingPostId
     * @param int $threadId
     * @param int $authorId
     * @return Post
     */
    public function createPost(
        $content,
        $promptingPostId,
        $threadId,
        $authorId
    ) {
        $content = $this->htmlPurifierService->clean($content);

        $post = new Post();
        $post->setContent($content);
        $post->setPromptingPostId($promptingPostId);
        $post->setThreadId($threadId);
        $post->setAuthorId($authorId);
        $post->setState(Post::STATE_PUBLISHED);
        $post->setPublishedOn(Carbon::now()->toDateTimeString());
        $post->persist();

        return $post;
    }
}