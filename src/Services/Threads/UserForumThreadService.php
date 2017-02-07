<?php

namespace Railroad\Railforums\Services\Threads;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railforums\DataMappers\ThreadReadDataMapper;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railforums\Entities\ThreadRead;
use Railroad\Railforums\Services\HTMLPurifierService;
use Railroad\Railmap\Helpers\RailmapHelpers;

class UserForumThreadService
{
    protected $htmlPurifierService;
    protected $threadDataMapper;
    protected $threadReadDataMapper;
    protected $userCloakDataMapper;

    protected $accessibleStates = [Post::STATE_PUBLISHED];

    public function __construct(
        HTMLPurifierService $htmlPurifierService,
        ThreadDataMapper $threadDataMapper,
        ThreadReadDataMapper $threadReadDataMapper,
        UserCloakDataMapper $userCloakDataMapper
    ) {
        $this->htmlPurifierService = $htmlPurifierService;
        $this->threadDataMapper = $threadDataMapper;
        $this->threadReadDataMapper = $threadReadDataMapper;
        $this->userCloakDataMapper = $userCloakDataMapper;
    }

    /**
     * @param $amount
     * @param $page
     * @param $categoryId
     * @param bool $pinned
     * @return Thread|Thread[]
     */
    public function getThreads(
        $amount,
        $page,
        $categoryId,
        $pinned = false
    ) {
        return $this->threadDataMapper->getWithQuery(
            function (Builder $builder) use (
                $amount,
                $page,
                $categoryId,
                $pinned
            ) {
                return $builder->limit($amount)
                    ->skip($amount * ($page - 1))
                    ->orderByRaw('last_post_published_on desc, id desc')
                    ->whereIn('forum_threads.state', $this->accessibleStates)
                    ->where('pinned', $pinned)
                    ->where('category_id', $categoryId)
                    ->get();
            }
        );
    }

    /**
     * @param $id
     * @return Thread
     */
    public function getThread($id)
    {
        return $this->threadDataMapper->get($id);
    }

    /**
     * @param $id
     * @param $readerId
     * @param null $dateTimeString
     * @return bool
     */
    public function updateThreadRead($id, $readerId, $dateTimeString = null)
    {
        if (is_null($dateTimeString)) {
            $dateTime = Carbon::now();
        } else {
            $dateTime = Carbon::parse($dateTimeString);
        }

        $threadRead = $this->threadReadDataMapper->get($id);

        if (empty($threadRead)) {
            $threadRead = new ThreadRead();
            $threadRead->setThreadId($id);
            $threadRead->setReaderId($readerId);
        }

        $threadRead->setReadOn($dateTime->toDateTimeString());
        $threadRead->persist();

        return true;
    }

    /**
     * @param $categoryId
     * @return int
     */
    public function getThreadCount($categoryId)
    {
        return $this->threadDataMapper->count(
            function (Builder $builder) use ($categoryId) {
                return $builder->whereIn('forum_threads.state', $this->accessibleStates)->where(
                    'category_id',
                    $categoryId
                )->where('pinned', false);
            }
        );
    }

    /**
     * @param $id
     * @param string $title
     * @return bool
     */
    public function updateThreadTitle($id, $title)
    {
        $thread = $this->threadDataMapper->get($id);

        if ($thread->getAuthorId() == $this->userCloakDataMapper->getCurrentId() && !empty($thread)) {
            $thread->setTitle($title);
            $thread->setSlug(RailmapHelpers::sanitizeForSlug($title));
            $thread->persist();

            return true;
        }

        return false;
    }

    /**
     * @param string $title
     * @param string $firstPostContent
     * @param int $categoryId
     * @param int $authorId
     * @param bool $pinned
     * @param bool $locked
     * @return Thread
     */
    public function createThread(
        $title,
        $firstPostContent,
        $categoryId,
        $authorId,
        $pinned = false,
        $locked = false
    ) {
        $firstPostContent = $this->htmlPurifierService->clean($firstPostContent);

        $thread = new Thread();
        $thread->setTitle($title);
        $thread->setSlug(RailmapHelpers::sanitizeForSlug($title));
        $thread->setCategoryId($categoryId);
        $thread->setAuthorId($authorId);
        $thread->setPinned($pinned);
        $thread->setLocked($locked);
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->setPublishedOn(Carbon::now()->toDateTimeString());
        $thread->persist();

        $post = new Post();
        $post->setThreadId($thread->getId());
        $post->setAuthorId($authorId);
        $post->setContent($firstPostContent);
        $post->setState(Thread::STATE_PUBLISHED);
        $post->setPublishedOn($thread->getPublishedOn());
        $post->persist();

        return $thread;
    }
}