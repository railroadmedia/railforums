<?php

namespace Railroad\Railforums\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Railforums\Requests\PostCreateRequest;
use Railroad\Railforums\Requests\PostUpdateRequest;
use Railroad\Railforums\DataMappers\PostDataMapper;
use Railroad\Railforums\Services\PostLikes\ForumPostLikeService;
use Railroad\Railforums\Services\Posts\UserForumPostService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumPostController extends Controller
{
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
     * UserForumPostController constructor.
     *
     * @param ForumPostLikeService $postLikeService
     * @param UserForumPostService $postService
     * @param PostDataMapper $postDataMapper
     */
    public function __construct(
        ForumPostLikeService $postLikeService,
        UserForumPostService $postService,
        PostDataMapper $postDataMapper
    ) {
        $this->postLikeService = $postLikeService;
        $this->postService = $postService;
        $this->postDataMapper = $postDataMapper;

        $this->middleware(config('railforums.controller_middleware'));
    }

    /**
     * @param Request $request
     * @param integer $id
     *
     * @return RedirectResponse
     */
    public function like(Request $request, $id)
    {
        $post = $this->postDataMapper->get($id);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        $this->postLikeService->likePost($id);

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
        $this->postLikeService->unLikePost($id);

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
        $content = $request->get('content');
        $promptingPostId = $request->get('prompting_post_id');
        $threadId = $request->get('thread_id');

        $this->postService
            ->createPost($content, $promptingPostId, $threadId);

        $message = ['success' => true];

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
        $content = $request->get('content');

        $result = $this->postService
                ->updatePostContent($id, $content);

        if (!$result) {
            throw new NotFoundHttpException();
        }

        $message = ['success' => true];

        return $request->has('redirect') ?
            redirect()->away($request->get('redirect'))->with($message) :
            redirect()->back()->with($message);
    }
}
