<?php

namespace Railroad\Railforums\EventListeners;

use Railroad\Railforums\Events\ThreadCreated;
use Railroad\Railforums\Events\ThreadDeleted;
use Railroad\Railforums\Events\ThreadUpdated;
use Railroad\Railforums\Repositories\CategoryRepository;
use Railroad\Railforums\Repositories\ThreadFollowRepository;
use Railroad\Railforums\Repositories\ThreadRepository;

class ThreadEventListener
{
    /**
     * @var ThreadFollowRepository
     */
    protected $threadFollowRepository;

    /**
     * @var ThreadRepository
     */
    protected $threadRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    public function __construct(
        ThreadFollowRepository $threadFollowRepository,
        ThreadRepository $threadRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->threadFollowRepository = $threadFollowRepository;
        $this->threadRepository = $threadRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function onCreated(ThreadCreated $threadCreated)
    {
        $thread = $this->threadRepository->read($threadCreated->getThreadId());

        $this->threadFollowRepository->follow($thread->id, $thread->author_id);
    }

    public function onDeleted(ThreadDeleted $threadDeleted)
    {
        $thread = $this->threadRepository->read($threadDeleted->getThreadId());

        $lastPostOnDiscussion = $this->categoryRepository->calculateLastPostId($thread['category_id']);
        $categoryThreadsCount = $this->threadRepository->getThreadsCount([$thread['category_id']]);
        $this->categoryRepository->update($thread['category_id'], [
            'last_post_id' => $lastPostOnDiscussion->post_id,
            'post_count' => $categoryThreadsCount,
        ]);
    }

    /**
     * Handle thread updated event - recalculate category metadata when thread is moved
     *
     * @param ThreadUpdated $event
     */
    public function onUpdated(ThreadUpdated $event)
    {
        $oldCategoryId = $event->getOldCategoryId();

        // Only proceed if this was a category change
        if (!$oldCategoryId) {
            return;
        }

        // Get the updated thread to find new category
        $thread = $this->threadRepository->read($event->getThreadId());
        if (!$thread) {
            return;
        }

        $newCategoryId = $thread->category_id;

        // Double-check category actually changed (shouldn't happen but safety check)
        if ($oldCategoryId == $newCategoryId) {
            return;
        }

        // Update OLD category metadata
        $oldCategoryLastPost = $this->categoryRepository->calculateLastPostId($oldCategoryId);
        $oldCategoryThreadCount = $this->threadRepository->getThreadsCount([$oldCategoryId]);
        $this->categoryRepository->update($oldCategoryId, [
            'last_post_id' => $oldCategoryLastPost->post_id ?? null,
            'post_count' => $oldCategoryThreadCount,
        ]);

        // Update NEW category metadata
        $newCategoryLastPost = $this->categoryRepository->calculateLastPostId($newCategoryId);
        $newCategoryThreadCount = $this->threadRepository->getThreadsCount([$newCategoryId]);
        $this->categoryRepository->update($newCategoryId, [
            'last_post_id' => $newCategoryLastPost->post_id ?? null,
            'post_count' => $newCategoryThreadCount,
        ]);
    }
}