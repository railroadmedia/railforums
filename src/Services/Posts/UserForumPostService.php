<?php

namespace Railroad\Railforums\Services\Posts;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Railroad\Railforums\DataMappers\PostDataMapper;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Services\HTMLPurifierService;

class UserForumPostService
{
    protected $htmlPurifierService;
    protected $postDataMapper;
    protected $userCloakDataMapper;

    protected $accessibleStates = [Post::STATE_PUBLISHED];

    public function __construct(
        HTMLPurifierService $htmlPurifierService,
        PostDataMapper $postDataMapper,
        UserCloakDataMapper $userCloakDataMapper
    ) {
        $this->htmlPurifierService = $htmlPurifierService;
        $this->postDataMapper = $postDataMapper;
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
                    ->orderByRaw('published_on asc')
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

        if ($post->getAuthorId() == $this->userCloakDataMapper->getCurrentId() && !empty($post)) {
            $post->destroy();

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
     * @return bool
     * @internal param string $title
     */
    public function updatePostContent($id, $content)
    {
        $post = $this->postDataMapper->get($id);

        if ($post->getAuthorId() == $this->userCloakDataMapper->getCurrentId() && !empty($post)) {
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

        return $post;
    }
}