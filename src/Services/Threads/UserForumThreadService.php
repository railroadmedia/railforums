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
use Railroad\Railforums\Events\ThreadCreated;
use Railroad\Railforums\Events\ThreadUpdated;
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
     * @param array|null $categoryIds
     * @param bool $pinned
     * @param bool $followed
     * @return Thread|Thread[]
     */
    public function getThreads(
        $amount,
        $page,
        $categoryIds,
        $pinned = false,
        $followed = null
    ) {
        return $this->threadDataMapper->getWithQuery(
            function (Builder $builder) use (
                $amount,
                $page,
                $categoryIds,
                $pinned,
                $followed
            ) {
                if ($followed === true) {
                    $builder->whereExists(
                        function (Builder $builder) {
                            return $builder
                                ->selectRaw('*')
                                ->from('forum_thread_follows')
                                ->limit(1)
                                ->where('follower_id', $this->userCloakDataMapper->getCurrentId())
                                ->whereRaw('forum_thread_follows.thread_id = forum_threads.id');
                        }
                    );
                }

                if (!empty($categoryIds)) {
                    $builder->whereIn('category_id', $categoryIds);

                }

                return $builder->limit($amount)
                    ->skip($amount * ($page - 1))
                    ->orderByRaw('last_post_published_on desc, id desc')
                    ->whereIn('forum_threads.state', $this->accessibleStates)
                    ->where('pinned', $pinned);

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
     * @param $categoryIds
     * @param null $followed
     * @return int
     */
    public function getThreadCount($categoryIds, $followed = null)
    {
        return $this->threadDataMapper->ignoreCache()->count(
            function (Builder $builder) use ($categoryIds, $followed) {
                if ($followed === true) {
                    $builder->whereNotNull('forum_thread_follows.id');
                } elseif ($followed === true) {
                    $builder->whereNull('forum_thread_follows.id');
                }

                if (!empty($categoryIds)) {
                    $builder->whereIn('category_id', $categoryIds);
                }

                return $builder->whereIn('forum_threads.state', $this->accessibleStates)->where('pinned', false);
            },
            $this->threadDataMapper->databaseManager()->connection()->raw('distinct(forum_threads.id)')
        );
    }

    /**
     * @param $id
     * @param string $title
     * @return Thread|null
     */
    public function updateThreadTitle($id, $title)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread) && $thread->getAuthorId() == $this->userCloakDataMapper->getCurrentId()) {
            $thread->setTitle($title);
            $thread->setSlug(RailmapHelpers::sanitizeForSlug($title));
            $thread->persist();

            $this->threadDataMapper->flushCache();

            event(new ThreadUpdated($id, $this->userCloakDataMapper->getCurrentId()));

            return $thread;
        }

        return null;
    }

    /**
     * @param $id
     * @param array $attributes
     * @return Thread|null
     */
    public function updateThread($id, array $attributes)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread) && $this->userCloakDataMapper->getCurrent()->canEditAnyThreads()) {
            $thread->fill($attributes);
            $thread->setSlug(RailmapHelpers::sanitizeForSlug($thread->getTitle()));
            $thread->persist();

            $this->threadDataMapper->flushCache();

            event(new ThreadUpdated($id, $this->userCloakDataMapper->getCurrentId()));

            return $thread;
        }

        return null;
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
        $thread->setPostCount(1);
        $thread->persist();

        $post = new Post();
        $post->setThreadId($thread->getId());
        $post->setAuthorId($authorId);
        $post->setContent($firstPostContent);
        $post->setState(Thread::STATE_PUBLISHED);
        $post->setPublishedOn($thread->getPublishedOn());
        $post->persist();

        $this->threadDataMapper->flushCache();

        event(new ThreadCreated($thread->getId(), $this->userCloakDataMapper->getCurrentId()));

        return $thread;
    }
}