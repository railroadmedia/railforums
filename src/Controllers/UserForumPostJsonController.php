<?php

namespace Railroad\Railforums\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Railforums\Contracts\UserProviderInterface;
use Railroad\Railforums\Repositories\PostLikeRepository;
use Railroad\Railforums\Repositories\PostReplyRepository;
use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Railforums\Repositories\ThreadRepository;
use Railroad\Railforums\Requests\PostJsonCreateRequest;
use Railroad\Railforums\Requests\PostJsonIndexRequest;
use Railroad\Railforums\Requests\PostJsonUpdateRequest;
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
     * @var ThreadRepository
     */
    protected $threadRepository;
    /**
     * @var UserProviderInterface
     */
    protected $userProvider;

    /**
     * UserForumPostJsonController constructor.
     *
     * @param PostLikeRepository $postLikeRepository
     * @param PostReplyRepository $postReplyRepository
     * @param PostRepository $postRepository
     * @param PermissionService $permissionService
     */
    public function __construct(
        PostLikeRepository $postLikeRepository,
        PostReplyRepository $postReplyRepository,
        PostRepository $postRepository,
        PermissionService $permissionService,
        ThreadRepository $threadRepository,
        UserProviderInterface $userProvider
    ) {
        $this->postLikeRepository = $postLikeRepository;
        $this->postReplyRepository = $postReplyRepository;
        $this->postRepository = $postRepository;
        $this->permissionService = $permissionService;
        $this->threadRepository = $threadRepository;
        $this->userProvider = $userProvider;

        $this->middleware(ConfigService::$controllerMiddleware);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function report($id)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'report-posts');

        $post = $this->postRepository->read($id);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        (new AnonymousNotifiable)->route(
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
        $this->permissionService->canOrThrow(auth()->id(), 'like-posts');

        $post = $this->postRepository->read($id);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        $now =
            Carbon::now()
                ->toDateTimeString();

        $postLike = $this->postLikeRepository->create([
                'post_id' => $post->id,
                'liker_id' => auth()->id(),
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
        $this->permissionService->canOrThrow(auth()->id(), 'like-posts');

        $postLikes =
            $this->postLikeRepository->query()
                ->where([
                    'post_id' => $id,
                    'liker_id' => auth()->id(),
                ])
                ->get();


        if (empty($postLikes)) {
            throw new NotFoundHttpException();
        }

        foreach ($postLikes as $postLike) {
            $this->postLikeRepository->destroy($postLike->id);
        }

        return response()->json(['success' => true]);
    }

    /**
     * @param PostJsonIndexRequest $request
     *
     * @return JsonResponse
     */
    public function index(PostJsonIndexRequest $request)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'index-posts');

        $amount = $request->get('amount') ? (int)$request->get('amount') : self::AMOUNT;
        $page = $request->get('page') ? (int)$request->get('page') : self::PAGE;
        $threadId = (int)$request->get('thread_id');

        $posts = $this->postRepository->getDecoratedPosts($amount, $page, $threadId);

        $postsCount = $this->postRepository->getPostsCount($threadId);

        $response = [
            'posts' => $posts,
            'count' => $postsCount,
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
        $this->permissionService->canOrThrow(auth()->id(), 'show-posts');

        $posts = $this->postRepository->getDecoratedPostsByIds([$id]);

        if (!$posts || $posts->isEmpty()) {
            throw new NotFoundHttpException();
        }

        $post =
            $posts->first()
                ->getArrayCopy();

        $posts = null;

        $post['reply_parents'] =
            $this->postReplyRepository->getPostReplyParents($post['id'])
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
        $this->permissionService->canOrThrow(auth()->id(), 'create-posts');

        $now =
            Carbon::now()
                ->toDateTimeString();
        $authorId = auth()->id();

        $post = $this->postRepository->create(
            array_merge($request->only([
                    'thread_id',
                    'content',
                    'prompting_post_id',
                ]), [
                    'state' => PostRepository::STATE_PUBLISHED,
                    'author_id' => $authorId,
                    'published_on' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
        );

        $parentIds = $request->get('parent_ids', []);

        if (!empty($parentIds)) {
            $replies = [];

            foreach ($parentIds as $parentId) {
                $replies[] = [
                    'child_post_id' => $post->id,
                    'parent_post_id' => $parentId,
                ];
            }

            $this->postReplyRepository->insert($replies);

            $post['reply_parents'] =
                $this->postReplyRepository->getPostReplyParents($post['id'])
                    ->all();
        }

        $allPostIdsInThread = collect(
            $this->postRepository->getAllPostIdsInThread($post['thread_id'])
        )
            ->pluck('id')
            ->all();
        $postPositionInThread = array_search($post['id'], $allPostIdsInThread);

        $post['page'] = ceil(($postPositionInThread + 1) / 10);

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
        $post = $this->postRepository->read($id);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        if ($post['author_id'] != auth()->id()) {
            $this->permissionService->canOrThrow(auth()->id(), 'update-posts');
        }

        $post = $this->postRepository->update(
            $id,
            array_merge($this->permissionService->columns(auth()->id(), 'update-posts', $request->all(), ['content']), [
                    'updated_at' => Carbon::now()
                        ->toDateTimeString(),
                ])
        );

        return response()->json($post);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function delete($id)
    {
        $post = $this->postRepository->read($id);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        if ($post['author_id'] != auth()->id()) {
            $this->permissionService->canOrThrow(auth()->id(), 'delete-posts');
        }

        $result = $this->postRepository->delete($id);

        if (!$result) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse(null, 204);
    }

    /**
     * @param Request $request
     * @param $postId
     * @return JsonResponse
     */
    public function getPostLikes(Request $request, $postId)
    {
        $postLikes = $this->postLikeRepository->getPostLikes(
            $postId,
            $request->get('limit', 10),
            $request->get('page', 1)
        );

        $likerIds =
            $postLikes->pluck('liker_id')
                ->toArray();

        $likers = $this->userProvider->getUsersByIds($likerIds);

        $results = [];
        foreach ($likers as $liker) {
            $results[] = [
                'user_id' => $liker->getId(),
                'authorId' => $liker->getId(),
                'display_name' => $liker->getDisplayName(),
                'avatar_url' => $liker->getProfilePictureUrl() ?? config('railforums.author_default_avatar_url'),
            ];

        }
        return response()->json($results);
    }
}
