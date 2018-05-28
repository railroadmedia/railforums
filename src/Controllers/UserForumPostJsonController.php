<?php

namespace Railroad\Railforums\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\DataMappers\PostDataMapper;
use Railroad\Railforums\Requests\PostJsonIndexRequest;
use Railroad\Railforums\Requests\PostJsonCreateRequest;
use Railroad\Railforums\Requests\PostJsonUpdateRequest;
use Railroad\Railforums\Services\Posts\UserForumPostService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumPostJsonController extends Controller
{
    const AMOUNT = 10;
    const PAGE = 1;

    /**
     * @var UserForumPostService
     */
    protected $service;

    /**
     * @var UserCloakDataMapper
     */
    protected $userCloakDataMapper;

    /**
     * @var PostDataMapper
     */
    protected $postDataMapper;

    /**
     * ThreadController constructor.
     *
     * @param UserForumPostService $service
     * @param UserCloakDataMapper $userCloakDataMapper
     * @param PostDataMapper $postDataMapper
     */
    public function __construct(
        UserForumPostService $service,
        UserCloakDataMapper $userCloakDataMapper,
        PostDataMapper $postDataMapper
    ) {
        $this->service = $service;
        $this->userCloakDataMapper = $userCloakDataMapper;
        $this->postDataMapper = $postDataMapper;
    }

    /**
     * @param PostJsonIndexRequest $request
     *
     * @return JsonResponse
     */
    public function index(PostJsonIndexRequest $request)
    {
        $amount = $request->get('amount') ?
                    (int) $request->get('amount') : self::AMOUNT;
        $page = $request->get('page') ?
                    (int) $request->get('page') : self::PAGE;
        $threadId = (int) $request->get('thread_id');

        $posts = $this->service
            ->getPosts($amount, $page, $threadId);

        $response = [];

        foreach ($posts as $post) {
            $response[] = $post->flatten();
        }

        return response()->json($response);
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        $post = $this->postDataMapper->get($id);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        return response()->json($post->flatten());
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

        $post = $this->service
            ->createPost($content, $promptingPostId, $threadId);

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

        $post = $this->service
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
