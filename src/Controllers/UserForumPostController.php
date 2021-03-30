<?php

namespace Railroad\Railforums\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Railforums\Contracts\UserProviderInterface;
use Railroad\Railforums\Requests\PostCreateRequest;
use Railroad\Railforums\Requests\PostUpdateRequest;
use Railroad\Railforums\Repositories\PostLikeRepository;
use Railroad\Railforums\Repositories\PostReplyRepository;
use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Railforums\Services\ConfigService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumPostController extends Controller
{
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
     * @var UserProviderInterface
     */
    protected $userProvider;

    /**
     * UserForumPostController constructor.
     *
     * @param PostLikeRepository $postLikeRepository
     * @param PostReplyRepository $postReplyRepository
     * @param PostRepository $postRepository
     * @param PermissionService $permissionService
     * @param UserProviderInterface $userProvider
     */
    public function __construct(
        PostLikeRepository $postLikeRepository,
        PostReplyRepository $postReplyRepository,
        PostRepository $postRepository,
        PermissionService $permissionService,
        UserProviderInterface $userProvider
    ) {
        $this->postLikeRepository = $postLikeRepository;
        $this->postReplyRepository = $postReplyRepository;
        $this->postRepository = $postRepository;
        $this->permissionService = $permissionService;
        $this->userProvider = $userProvider;

        $this->middleware(ConfigService::$controllerMiddleware);
    }

    /**
     * @param Request $request
     * @param integer $id
     *
     * @return RedirectResponse
     */
    public function like(Request $request, $id)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'like-posts');

        $post = $this->postRepository->read($id);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        $now = Carbon::now()->toDateTimeString();

        $this->postLikeRepository->create([
            'post_id' => $post->id,
            'liker_id' => $this->userProvider->getCurrentUser()->getId(),
            'liked_on' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $message = ['success' => true];

        return $request->has('redirect') ?
            redirect()->away($request->get('redirect'))->with($message) :
            redirect()->back()->with($message);
    }

    /**
     * @param Request $request
     * @param integer $id
     *
     * @return RedirectResponse
     */
    public function unlike(Request $request, $id)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'like-posts');

        $postLike = $this->postLikeRepository->read($id);

        if (!$postLike) {
            throw new NotFoundHttpException();
        }

        $this->postLikeRepository->destroy($postLike->id);

        $message = ['success' => true];

        return $request->has('redirect') ?
            redirect()->away($request->get('redirect'))->with($message) :
            redirect()->back()->with($message);
    }

    /**
     * @param PostCreateRequest $request
     *
     * @return RedirectResponse
     */
    public function store(PostCreateRequest $request)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'create-posts');

        $now = Carbon::now()->toDateTimeString();
        $authorId = $this->userProvider->getCurrentUser()->getId();

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
        }

        $message = ['success' => true];

        return redirect()->to('/members/forums/jump-to-post/' . $post->id);

        return $request->has('redirect') ?
            redirect()->away($request->get('redirect'))->with($message) :
            redirect()->back()->with($message);
    }

    /**
     * @param PostUpdateRequest $request
     * @param integer $id
     *
     * @return RedirectResponse
     */
    public function update(PostUpdateRequest $request, $id)
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

        $message = ['success' => true];

        return redirect()->to('/members/forums/jump-to-post/' . $post->id);

        return $request->has('redirect') ?
            redirect()->away($request->get('redirect'))->with($message) :
            redirect()->back()->with($message);
    }
}
