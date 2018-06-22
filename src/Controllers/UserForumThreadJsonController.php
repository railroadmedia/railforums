<?php

namespace Railroad\Railforums\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\Requests\ThreadJsonIndexRequest;
use Railroad\Railforums\Requests\ThreadJsonCreateRequest;
use Railroad\Railforums\Requests\ThreadJsonUpdateRequest;
use Railroad\Railforums\Repositories\ThreadRepository;
use Railroad\Railforums\Repositories\ThreadReadRepository;
use Railroad\Railforums\Repositories\ThreadFollowRepository;
use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Railforums\Transformers\ThreadTransformer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumThreadJsonController extends Controller
{
    const AMOUNT = 10;
    const PAGE = 1;

    /**
     * @var UserCloakDataMapper
     */
    protected $userCloakDataMapper;

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
     * ThreadController constructor.
     *
     * @param UserCloakDataMapper $userCloakDataMapper
     * @param ThreadRepository $threadRepository
     * @param ThreadReadRepository $threadReadRepository
     * @param ThreadFollowRepository $threadFollowRepository
     * @param PostRepository $postRepository
     * @param PermissionService $permissionService
     */
    public function __construct(
        UserCloakDataMapper $userCloakDataMapper,
        ThreadRepository $threadRepository,
        ThreadReadRepository $threadReadRepository,
        ThreadFollowRepository $threadFollowRepository,
        PostRepository $postRepository,
        PermissionService $permissionService
    ) {
        $this->userCloakDataMapper = $userCloakDataMapper;
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
        if (!$this->permissionService->can(auth()->id(), 'read-threads')) {
            throw new NotFoundHttpException();
        }

        $thread = $this->threadRepository->read($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        $now = Carbon::now()->toDateTimeString();

        $threadRead = $this->threadReadRepository->create([
            'thread_id' => $thread->id,
            'reader_id' => $this->userCloakDataMapper->getCurrentId(),
            'read_on' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return response()->json($threadRead);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function follow($id)
    {
        if (!$this->permissionService->can(auth()->id(), 'follow-threads')) {
            throw new NotFoundHttpException();
        }

        $thread = $this->threadRepository->read($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        $now = Carbon::now()->toDateTimeString();

        $threadFollow = $this->threadFollowRepository->create([
            'thread_id' => $thread->id,
            'follower_id' => $this->userCloakDataMapper->getCurrentId(),
            'followed_on' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return response()->json($threadFollow);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function unfollow($id)
    {
        if (!$this->permissionService->can(auth()->id(), 'follow-threads')) {
            throw new NotFoundHttpException();
        }

        $threadFollow = $this->threadFollowRepository->read($id);

        if (!$threadFollow) {
            throw new NotFoundHttpException();
        }

        $this->threadFollowRepository->destroy($threadFollow->id);

        return new JsonResponse(null, 204);
    }

    /**
     * @param ThreadJsonIndexRequest $request
     *
     * @return JsonResponse
     */
    public function index(ThreadJsonIndexRequest $request)
    {
        if (!$this->permissionService->can(auth()->id(), 'index-threads')) {
            throw new NotFoundHttpException();
        }

        $amount = $request->get('amount') ?
                    (int) $request->get('amount') : self::AMOUNT;
        $page = $request->get('page') ?
                    (int) $request->get('page') : self::PAGE;
        $categoryIds = $request->get('category_ids', null);
        $pinned = (boolean) $request->get('pinned');
        $followed = $request->has('followed') ?
            (boolean) $request->get('followed') : null;

        $threads = $this->threadRepository
            ->getDecoratedThreads(
                $amount,
                $page,
                $categoryIds,
                $pinned,
                $followed
            );

        $threadsCount = $this->threadRepository
            ->getThreadsCount(
                $categoryIds,
                $pinned,
                $followed
            );

        return fractal()
                ->collection($threads, ThreadTransformer::class)
                ->addMeta(['count' => $threadsCount])
                ->respond();
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        if (!$this->permissionService->can(auth()->id(), 'show-threads')) {
            throw new NotFoundHttpException();
        }

        $threads = $this->threadRepository->getDecoratedThreadsByIds([$id]);

        if (!$threads || $threads->isEmpty()) {
            throw new NotFoundHttpException();
        }

        return response()->json($threads->first());
    }

    /**
     * @param ThreadJsonCreateRequest $request
     *
     * @return JsonResponse
     */
    public function store(ThreadJsonCreateRequest $request)
    {
        if (!$this->permissionService->can(auth()->id(), 'create-threads')) {
            throw new NotFoundHttpException();
        }

        $now = Carbon::now()->toDateTimeString();
        $authorId = $this->userCloakDataMapper->getCurrentId();

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

        $threads = $this->threadRepository
                    ->getDecoratedThreadsByIds([$thread->id]);

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
        if (!$this->permissionService->can(auth()->id(), 'update-threads')) {
            throw new NotFoundHttpException();
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
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            )
        );

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        $threads = $this->threadRepository
                    ->getDecoratedThreadsByIds([$thread->id]);

        return response()->json($threads->first());
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function delete($id)
    {
        if (!$this->permissionService->can(auth()->id(), 'delete-threads')) {
            throw new NotFoundHttpException();
        }

        $result = $this->threadRepository->delete($id);

        if (!$result) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse(null, 204);
    }
}
