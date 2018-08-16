<?php

namespace Railroad\Railforums\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Notifications\AnonymousNotifiable;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\Requests\PostJsonIndexRequest;
use Railroad\Railforums\Requests\PostJsonCreateRequest;
use Railroad\Railforums\Requests\PostJsonUpdateRequest;
use Railroad\Railforums\Repositories\PostLikeRepository;
use Railroad\Railforums\Repositories\PostReplyRepository;
use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Railforums\Services\ConfigService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumPostJsonController extends Controller
{
    const AMOUNT = 10;
    const PAGE = 1;

    /**
     * @var PostLikeRepository
     */
    protected $postLikeRepository;

    /**
     * @var PostReplyRepository
     */
    protected $postReplyRepository;

    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * @var UserCloakDataMapper
     */
    protected $userCloakDataMapper;

    /**
     * ThreadController constructor.
     *
     * @param PostLikeRepository $postLikeRepository
     * @param PostReplyRepository $postReplyRepository
     * @param PostRepository $postRepository
     * @param PermissionService $permissionService
     * @param UserCloakDataMapper $userCloakDataMapper
     */
    public function __construct(
        PostLikeRepository $postLikeRepository,
        PostReplyRepository $postReplyRepository,
        PostRepository $postRepository,
        PermissionService $permissionService,
        UserCloakDataMapper $userCloakDataMapper
    ) {
        $this->postLikeRepository = $postLikeRepository;
        $this->postReplyRepository = $postReplyRepository;
        $this->postRepository = $postRepository;
        $this->permissionService = $permissionService;
        $this->userCloakDataMapper = $userCloakDataMapper;

        $this->middleware(ConfigService::$controllerMiddleware);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function report($id)
    {
        if (!$this->permissionService->can(auth()->id(), 'report-posts')) {
            throw new NotFoundHttpException();
        }

        $post = $this->postRepository->read($id);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        (new AnonymousNotifiable)
            ->route(
                ConfigService::$postReportNotificationChannel,
                ConfigService::$postReportNotificationRecipients
            )
            ->notify(
                new ConfigService::$postReportNotificationClass(
                    $post->getArrayCopy()
                )
            );

        return new JsonResponse(null, 204);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function like($id)
    {
        if (!$this->permissionService->can(auth()->id(), 'like-posts')) {
            throw new NotFoundHttpException();
        }

        $post = $this->postRepository->read($id);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        $now = Carbon::now()->toDateTimeString();

        $postLike = $this->postLikeRepository->create([
            'post_id' => $post->id,
            'liker_id' => $this->userCloakDataMapper->getCurrentId(),
            'liked_on' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return response()->json($postLike);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function unlike($id)
    {
        if (!$this->permissionService->can(auth()->id(), 'like-posts')) {
            throw new NotFoundHttpException();
        }

        $postLike = $this->postLikeRepository->query()->where('post_id', $id)->first();

        if (!$postLike) {
            throw new NotFoundHttpException();
        }

        $this->postLikeRepository->destroy($postLike->id);

        return new JsonResponse(null, 204);
    }

    /**
     * @param PostJsonIndexRequest $request
     *
     * @return JsonResponse
     */
    public function index(PostJsonIndexRequest $request)
    {
        if (!$this->permissionService->can(auth()->id(), 'index-posts')) {
            throw new NotFoundHttpException();
        }

        $amount = $request->get('amount') ?
                    (int) $request->get('amount') : self::AMOUNT;
        $page = $request->get('page') ?
                    (int) $request->get('page') : self::PAGE;
        $threadId = (int) $request->get('thread_id');

        $posts = $this->postRepository
            ->getDecoratedPosts($amount, $page, $threadId);

        $postsCount = $this->postRepository->getPostsCount($threadId);

        $response = [
            'posts' => $posts,
            'count' => $postsCount
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
        if (!$this->permissionService->can(auth()->id(), 'show-posts')) {
            throw new NotFoundHttpException();
        }

        $posts = $this->postRepository->getDecoratedPostsByIds([$id]);

        if (!$posts || $posts->isEmpty()) {
            throw new NotFoundHttpException();
        }

        $post = $posts->first()->getArrayCopy();

        $posts = null;

        $post['reply_parents'] = $this->postReplyRepository
            ->getPostReplyParents($post['id'])
            ->all();

        return response()->json($post);
    }

    /**
     * @param PostJsonCreateRequest $request
     *
     * @return JsonResponse
     */
    public function store(PostJsonCreateRequest $request)
    {
        if (!$this->permissionService->can(auth()->id(), 'create-posts')) {
            throw new NotFoundHttpException();
        }

        $now = Carbon::now()->toDateTimeString();
        $authorId = $this->userCloakDataMapper->getCurrentId();

        $post = $this->postRepository->create(array_merge(
            $request->only([
                'thread_id',
                'content',
                'prompting_post_id'
            ]),
            [
                'state' => PostRepository::STATE_PUBLISHED,
                'author_id' => $authorId,
                'published_on' => $now,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ));

        $parentIds = $request->get('parent_ids', []);

        if (!empty($parentIds)) {
            $replies = [];

            foreach ($parentIds as $parentId) {
                $replies[] = [
                    'child_post_id' => $post->id,
                    'parent_post_id' => $parentId
                ];
            }

            $this->postReplyRepository->insert($replies);

            $post['reply_parents'] = $this->postReplyRepository
                ->getPostReplyParents($post['id'])
                ->all();
        }

        return response()->json($post);
    }

    /**
     * @param PostJsonUpdateRequest $request
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function update(PostJsonUpdateRequest $request, $id)
    {
        if (!$this->permissionService->can(auth()->id(), 'update-posts')) {
            throw new NotFoundHttpException();
        }

        $post = $this->postRepository->update(
            $id,
            array_merge(
                $this->permissionService->columns(
                    auth()->id(),
                    'update-posts',
                    $request->all(),
                    ['content']
                ),
                [
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            )
        );

        if (!$post) {
            throw new NotFoundHttpException();
        }

        return response()->json($post);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function delete($id)
    {
        if (!$this->permissionService->can(auth()->id(), 'delete-posts')) {
            throw new NotFoundHttpException();
        }

        $result = $this->postRepository->delete($id);

        if (!$result) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse(null, 204);
    }
}
