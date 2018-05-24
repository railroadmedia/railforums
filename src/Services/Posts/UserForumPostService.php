<?php

namespace Railroad\Railforums\Services\Posts;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Railroad\Railforums\DataMappers\PostDataMapper;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Events\PostCreated;
use Railroad\Railforums\Events\PostDeleted;
use Railroad\Railforums\Events\PostUpdated;
use Railroad\Railforums\Services\HTMLPurifierService;

class UserForumPostService
{
    protected $htmlPurifierService;
    protected $postDataMapper;
    protected $threadDataMapper;
    protected $userCloakDataMapper;

    protected $accessibleStates = [Post::STATE_PUBLISHED];

    public function __construct(
        HTMLPurifierService $htmlPurifierService,
        PostDataMapper $postDataMapper,
        ThreadDataMapper $threadDataMapper,
        UserCloakDataMapper $userCloakDataMapper
    ) {
        $this->htmlPurifierService = $htmlPurifierService;
        $this->postDataMapper = $postDataMapper;
        $this->threadDataMapper = $threadDataMapper;
        $this->userCloakDataMapper = $userCloakDataMapper;
    }

    /**
     * @param $amount
     * @param $page
     * @param $threadId
     * @return Post[]
     */
    public function getPosts($amount, $page, $threadId)
    {
        $this->postDataMapper->with = ['author', 'promptingPost', 'recentLikes'];

        return $this->postDataMapper->getWithQuery(
            function (Builder $builder) use (
                $amount,
                $page,
                $threadId
            ) {
                return $builder->limit($amount)
                    ->skip($amount * ($page - 1))
                    ->orderBy('published_on', 'asc')
                    ->where('thread_id', $threadId)
                    ->whereIn('state', $this->accessibleStates);
            }
        );
    }

    /**
     * @param $id
     * @return Post|null
     */
    public function getPost($id)
    {
        return $this->postDataMapper->get($id);
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
            $this->threadDataMapper->flushCache();

            event(new PostDeleted($id, $this->userCloakDataMapper->getCurrentId()));

            return true;
        }

        return false;
    }

    /**
     * @param $threadId
     * @return int
     */
    public function getThreadPostCount($threadId)
    {
        return $this->postDataMapper->ignoreCache()->count(
            function (Builder $builder) use ($threadId) {
                return $builder->whereIn('forum_posts.state', $this->accessibleStates)
                    ->where('thread_id', $threadId);
            }
        );
    }

    /**
     * @param $id
     * @param $content
     * @return Post|null
     */
    public function updatePostContent($id, $content)
    {
        $post = $this->postDataMapper->get($id);

        if (
            !empty($post)
            && $post->getAuthorId() == $this->userCloakDataMapper->getCurrentId()
        ) {
            $post->setContent($this->htmlPurifierService->clean($content));
            $post->setEditedOn(Carbon::now()->toDateTimeString());
            $post->persist();

            $this->postDataMapper->flushCache();

            event(new PostUpdated($id, $this->userCloakDataMapper->getCurrentId()));

            return $post;
        }

        return null;
    }

    /**
     * @param $id
     * @param $promptingPostId |null
     * @return Post|null
     */
    public function updatePostPromptingPostId($id, $promptingPostId)
    {
        $post = $this->postDataMapper->get($id);

        if (!empty($post)) {
            $post->setPromptingPostId($promptingPostId);
            $post->setEditedOn(Carbon::now()->toDateTimeString());
            $post->persist();

            $this->postDataMapper->flushCache();

            event(new PostUpdated($id, $this->userCloakDataMapper->getCurrentId()));

            return $post;
        }

        return null;
    }

    /**
     * @param string $content
     * @param int $promptingPostId
     * @param int $threadId
     * @return Post
     */
    public function createPost(
        $content,
        $promptingPostId,
        $threadId
    ) {
        $content = $this->htmlPurifierService->clean($content);

        $post = new Post();
        $post->setContent($content);
        $post->setPromptingPostId($promptingPostId);
        $post->setThreadId($threadId);
        $post->setAuthorId($this->userCloakDataMapper->getCurrentId());
        $post->setState(Post::STATE_PUBLISHED);
        $post->setPublishedOn(Carbon::now()->toDateTimeString());
        $post->persist();

        $this->postDataMapper->flushCache();
        $this->threadDataMapper->flushCache();

        event(new PostCreated($post->getId(), $this->userCloakDataMapper->getCurrentId()));

        return $post;
    }

    /**
     * @param $postId
     * @return bool
     */
    public function postFirstInThread($postId)
    {
        $post = $this->getPost($postId);

        $firstPost = $this->postDataMapper->getWithQuery(
                function (Builder $builder) use ($post) {
                    return $builder->orderByRaw('published_on asc')
                        ->where('thread_id', $post->getThreadId())
                        ->whereIn('state', $this->accessibleStates)->limit(1);
                }
            )[0] ?? null;

        if ($post->getId() == $firstPost->getId()) {
            return true;
        }

        return false;
    }
}