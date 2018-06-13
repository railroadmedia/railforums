<?php

namespace Railroad\Railforums\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railforums\Requests\ThreadJsonIndexRequest;
use Railroad\Railforums\Requests\ThreadJsonCreateRequest;
use Railroad\Railforums\Requests\ThreadJsonUpdateRequest;
use Railroad\Railforums\Services\ThreadFollows\ThreadFollowService;
use Railroad\Railforums\Services\Threads\UserForumThreadService;
use Railroad\Railforums\Services\Posts\ForumThreadReadService;
use Railroad\Railforums\Repositories\ThreadRepository;
use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Railforums\Services\ConfigService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumThreadJsonController extends Controller
{
    const AMOUNT = 10;
    const PAGE = 1;
    const STATE_PUBLISHED = 'published';

    /**
     * @var ForumThreadReadService
     */
    protected $threadReadService;

    /**
     * @var ThreadFollowService
     */
    protected $threadFollowService;

    /**
     * @var UserForumThreadService
     */
    protected $threadService;

    /**
     * @var UserCloakDataMapper
     */
    protected $userCloakDataMapper;

    /**
     * @var ThreadDataMapper
     */
    protected $threadDataMapper;

    /**
     * @var ThreadRepository
     */
    protected $threadRepository;

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
     * @param ForumThreadReadService $threadReadService
     * @param ThreadFollowService $threadFollowService
     * @param UserForumThreadService $threadService
     * @param UserCloakDataMapper $userCloakDataMapper
     * @param ThreadDataMapper $threadDataMapper
     * @param ThreadRepository $threadRepository
     * @param PostRepository $postRepository
     * @param PermissionService $permissionService
     */
    public function __construct(
        ForumThreadReadService $threadReadService,
        ThreadFollowService $threadFollowService,
        UserForumThreadService $threadService,
        UserCloakDataMapper $userCloakDataMapper,
        ThreadDataMapper $threadDataMapper,
        ThreadRepository $threadRepository,
        PostRepository $postRepository,
        PermissionService $permissionService
    ) {
        $this->threadReadService = $threadReadService;
        $this->threadFollowService = $threadFollowService;
        $this->threadService = $threadService;
        $this->userCloakDataMapper = $userCloakDataMapper;
        $this->threadDataMapper = $threadDataMapper;

        $this->threadRepository = $threadRepository;
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
        $thread = $this->threadDataMapper->get($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        $threadRead = $this->threadReadService->markThreadRead(
            $thread->getId(),
            $this->userCloakDataMapper->getCurrentId()
        );

        return response()->json($threadRead->flatten());
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function follow($id)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        $this->threadFollowService->follow(
            $thread->getId(),
            $this->userCloakDataMapper->getCurrentId()
        );

        return new JsonResponse(null, 204);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function unfollow($id)
    {
        $this->threadFollowService->unFollow(
            $id,
            $this->userCloakDataMapper->getCurrentId()
        );

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
            )
            ->toArray();

        $threadsCount = $this->threadRepository
            ->getThreadsCount(
                $categoryIds,
                $pinned,
                $followed
            );

        $response = [
            'threads' => $threads,
            'count' => $threadsCount
        ];

        return response()->json($response);
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
                    'slug' => self::sanitizeForSlug($request->get('title')),
                    'state' => self::STATE_PUBLISHED,
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
                'state' => self::STATE_PUBLISHED,
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
                $request->only(
                    [
                        'category_id',
                        'author_id',
                        'title',
                        'slug',
                        'pinned',
                        'locked',
                        'state',
                        'post_count',
                        'published_on',
                    ]
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

    /**
     * @param $string
     *
     * @return string
     */
    public static function sanitizeForSlug($string)
    {
        return strtolower(
            preg_replace(
                '/(\-)+/',
                '-',
                str_replace(' ', '-', preg_replace('/[^ \w]+/', '', str_replace('&', 'and', trim($string))))
            )
        );
    }
}
