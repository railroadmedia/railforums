<?php

namespace Railroad\Railforums\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Railforums\Repositories\ThreadFollowRepository;
use Railroad\Railforums\Repositories\ThreadReadRepository;
use Railroad\Railforums\Repositories\ThreadRepository;
use Railroad\Railforums\Requests\ThreadJsonCreateRequest;
use Railroad\Railforums\Requests\ThreadJsonIndexRequest;
use Railroad\Railforums\Requests\ThreadJsonUpdateRequest;
use Railroad\Railforums\Responses\JsonPaginatedResponse;
use Railroad\Railforums\Services\ConfigService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumThreadJsonController extends Controller
{
    const AMOUNT = 10;
    const PAGE = 1;

    /**
     * @var ThreadRepository
     */
    protected $threadRepository;

    /**
     * @var ThreadReadRepository
     */
    protected $threadReadRepository;

    /**
     * @var ThreadFollowRepository
     */
    protected $threadFollowRepository;

    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * @var PermissionService
     */
    private $permissionService;

    /**
     * UserForumThreadJsonController constructor.
     *
     * @param ThreadRepository $threadRepository
     * @param ThreadReadRepository $threadReadRepository
     * @param ThreadFollowRepository $threadFollowRepository
     * @param PostRepository $postRepository
     * @param PermissionService $permissionService
     */
    public function __construct(
        ThreadRepository $threadRepository,
        ThreadReadRepository $threadReadRepository,
        ThreadFollowRepository $threadFollowRepository,
        PostRepository $postRepository,
        PermissionService $permissionService
    ) {
        $this->threadRepository = $threadRepository;
        $this->threadReadRepository = $threadReadRepository;
        $this->threadFollowRepository = $threadFollowRepository;
        $this->postRepository = $postRepository;
        $this->permissionService = $permissionService;

        $this->middleware(ConfigService::$controllerMiddleware);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function read($id)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'read-threads');

        $thread = $this->threadRepository->read($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        $threadRead = $this->threadReadRepository->markRead(
            $thread->id,
            auth()->id()
        );

        return response()->json($threadRead);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function follow($id)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'follow-threads');

        $thread = $this->threadRepository->read($id);

        if (empty($thread)) {
            throw new NotFoundHttpException();
        }

        $now =
            Carbon::now()
                ->toDateTimeString();

        $threadFollow = $this->threadFollowRepository->create(
            [
                'thread_id' => $id,
                'follower_id' => auth()->id(),
                'followed_on' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        return response()->json($threadFollow);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function unfollow($id)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'follow-threads');

        $thread = $this->threadRepository->read($id);
        $currentUserId = auth()->id();

        if (empty($thread) || empty($currentUserId)) {
            throw new NotFoundHttpException();
        }

        $this->threadFollowRepository->query()
            ->where(
                [
                    'thread_id' => $id,
                    'follower_id' => $currentUserId,
                ]
            )
            ->delete();

        return new JsonResponse(null, 204);
    }

    /**
     * @param ThreadJsonIndexRequest $request
     *
     * @return JsonResponse
     */
    public function index(ThreadJsonIndexRequest $request)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'index-threads');

        $amount = $request->get('amount') ? (int)$request->get('amount') : self::AMOUNT;
        $page = $request->get('page') ? (int)$request->get('page') : self::PAGE;
        $categoryId = $request->get('category_id', null);
        $pinned = (boolean)$request->get('pinned');
        $followed = $request->has('followed') ? (boolean)$request->get('followed') : null;

        $threads = $this->threadRepository->getDecoratedThreads(
            $amount,
            $page,
            ($categoryId) ? [$categoryId] : [],
            $pinned,
            $followed
        )
            ->toArray();

        $threadsCount = $this->threadRepository->getThreadsCount(
            ($categoryId) ? [$categoryId] : [],
            $pinned,
            $followed
        );

        return new JsonPaginatedResponse(
            $threads, $threadsCount, null, 200
        );
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function show($id, Request $request)
    {
        //$this->permissionService->canOrThrow(auth()->id(), 'show-threads');

        $thread =
            $this->threadRepository->getDecoratedThreadsByIds([$id])
                ->first();

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        $amount = $request->get('amount', 20);
        $page = $request->get('page', 1);

        $thread['posts'] = $this->postRepository->getDecoratedPosts($amount, $page, $id);

        return response()->json($thread);
    }

    /**
     * @param ThreadJsonCreateRequest $request
     *
     * @return JsonResponse
     */
    public function store(ThreadJsonCreateRequest $request)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'create-threads');

        $now =
            Carbon::now()
                ->toDateTimeString();
        $authorId = auth()->id();

        $thread = $this->threadRepository->create(
            array_merge(
                $request->only(
                    [
                        'title',
                        'category_id',
                    ]
                ),
                [
                    'author_id' => $authorId,
                    'slug' => ThreadRepository::sanitizeForSlug(
                        $request->get('title')
                    ),
                    'state' => ThreadRepository::STATE_PUBLISHED,
                    'published_on' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            )
        );

        $this->postRepository->create(
            [
                'thread_id' => $thread->id,
                'author_id' => $authorId,
                'content' => $request->get('first_post_content'),
                'state' => PostRepository::STATE_PUBLISHED,
                'published_on' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $threads = $this->threadRepository->getDecoratedThreadsByIds([$thread->id]);

        return response()->json($threads->first());
    }

    /**
     * @param ThreadJsonUpdateRequest $request
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function update(ThreadJsonUpdateRequest $request, $id)
    {
        $thread = $this->threadRepository->read($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        if ($thread['author_id'] != auth()->id()) {
            $this->permissionService->canOrThrow(auth()->id(), 'update-threads');
        }

        $thread = $this->threadRepository->update(
            $id,
            array_merge(
                $this->permissionService->columns(
                    auth()->id(),
                    'update-threads',
                    $request->all(),
                    ['title']
                ),
                [
                    'updated_at' => Carbon::now()
                        ->toDateTimeString(),
                ]
            )
        );

        return response()->json($thread);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function delete($id)
    {
        $thread = $this->threadRepository->read($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        if ($thread['author_id'] != auth()->id()) {
            $this->permissionService->canOrThrow(auth()->id(), 'delete-threads');
        }

        $result = $this->threadRepository->delete($id);

        if (!$result) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse(null, 204);
    }
}
