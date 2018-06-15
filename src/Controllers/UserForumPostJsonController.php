<?php

namespace Railroad\Railforums\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\DataMappers\PostDataMapper;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Requests\PostJsonIndexRequest;
use Railroad\Railforums\Requests\PostJsonCreateRequest;
use Railroad\Railforums\Requests\PostJsonUpdateRequest;
use Railroad\Railforums\Repositories\PostLikeRepository;
use Railroad\Railforums\Repositories\PostReplyRepository;
use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Railforums\Services\PostLikes\ForumPostLikeService;
use Railroad\Railforums\Services\Posts\UserForumPostService;
use Railroad\Railforums\Services\PostReplies\PostReplyService;
use Railroad\Railforums\Services\ConfigService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumPostJsonController extends Controller
{
    const AMOUNT = 10;
    const PAGE = 1;

    /**
     * @var PostReplyService
     */
    protected $postReplyService;

    /**
     * @var ForumPostLikeService
     */
    protected $postLikeService;

    /**
     * @var UserForumPostService
     */
    protected $postService;

    /**
     * @var PostDataMapper
     */
    protected $postDataMapper;

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
     * @param ForumPostLikeService $postLikeService
     * @param UserForumPostService $postService
     * @param PostDataMapper $postDataMapper
     * @param PostLikeRepository $postLikeRepository
     * @param PostReplyRepository $postReplyRepository
     * @param PostRepository $postRepository
     * @param PermissionService $permissionService
     * @param UserCloakDataMapper $userCloakDataMapper
     */
    public function __construct(
        PostReplyService $postReplyService,
        ForumPostLikeService $postLikeService,
        UserForumPostService $postService,
        PostDataMapper $postDataMapper,
        PostLikeRepository $postLikeRepository,
        PostReplyRepository $postReplyRepository,
        PostRepository $postRepository,
        PermissionService $permissionService,
        UserCloakDataMapper $userCloakDataMapper
    ) {
        $this->postReplyService = $postReplyService;
        $this->postLikeService = $postLikeService;
        $this->postService = $postService;
        $this->postDataMapper = $postDataMapper;

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

        $postLike = $this->postLikeRepository->read($id);

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
        $content = $request->get('content');
        $promptingPostId = $request->get('prompting_post_id');
        $threadId = $request->get('thread_id');
        $parentIds = $request->get('parent_ids', []);

        $post = $this->postService
            ->createPost($content, $promptingPostId, $threadId, $parentIds);

        return response()->json($post->flatten());
    }

    /**
     * @param PostJsonUpdateRequest $request
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function update(PostJsonUpdateRequest $request, $id)
    {
        $content = $request->get('content');

        $post = $this->postService
                ->updatePostContent($id, $content);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        return response()->json($post->flatten());
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function delete($id)
    {
        $post = $this->postDataMapper->get($id);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        $post->destroy();

        $this->postDataMapper->flushCache();

        return new JsonResponse(null, 204);
    }
}
