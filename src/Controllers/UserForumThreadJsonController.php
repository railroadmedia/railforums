<?php

namespace Railroad\Railforums\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railforums\Requests\ThreadJsonIndexRequest;
use Railroad\Railforums\Requests\ThreadJsonCreateRequest;
use Railroad\Railforums\Requests\ThreadJsonUpdateRequest;
use Railroad\Railforums\Services\ThreadFollows\ThreadFollowService;
use Railroad\Railforums\Services\Threads\UserForumThreadService;
use Railroad\Railforums\Services\Posts\ForumThreadReadService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumThreadJsonController extends Controller
{
    const AMOUNT = 10;
    const PAGE = 1;

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
     * ThreadController constructor.
     *
     * @param ForumThreadReadService $threadReadService
     * @param ThreadFollowService $threadFollowService
     * @param UserForumThreadService $threadService
     * @param UserCloakDataMapper $userCloakDataMapper
     * @param ThreadDataMapper $threadDataMapper
     */
    public function __construct(
        ForumThreadReadService $threadReadService,
        ThreadFollowService $threadFollowService,
        UserForumThreadService $threadService,
        UserCloakDataMapper $userCloakDataMapper,
        ThreadDataMapper $threadDataMapper
    ) {
        $this->threadReadService = $threadReadService;
        $this->threadFollowService = $threadFollowService;
        $this->threadService = $threadService;
        $this->userCloakDataMapper = $userCloakDataMapper;
        $this->threadDataMapper = $threadDataMapper;

        $this->middleware(config('railforums.controller_middleware'));
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
        $amount = $request->get('amount') ?
                    (int) $request->get('amount') : self::AMOUNT;
        $page = $request->get('page') ?
                    (int) $request->get('page') : self::PAGE;
        $categoryIds = $request->get('category_ids', null);
        $pinned = (boolean) $request->get('pinned');
        $followed = $request->has('followed') ?
            (boolean) $request->get('followed') : null;

        $threads = $this->threadService
            ->getThreads($amount, $page, $categoryIds, $pinned, $followed);

        $threadsCount = $this->threadService
            ->getThreadCount($categoryIds, $followed);

        $response = [
            'threads' => [],
            'count' => $threadsCount
        ];

        foreach ($threads as $thread) {
            $response['threads'][] = $thread->flatten();
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
        $thread = $this->threadDataMapper->get($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        return response()->json($thread->flatten());
    }

    /**
     * @param ThreadJsonCreateRequest $request
     *
     * @return JsonResponse
     */
    public function store(ThreadJsonCreateRequest $request)
    {
        $title = $request->get('title');
        $firstPostContent = $request->get('first_post_content');
        $categoryId = $request->get('category_id');
        $authorId = $this->userCloakDataMapper->getCurrentId();

        $thread = $this->threadService
            ->createThread($title, $firstPostContent, $categoryId, $authorId);

        return response()->json($thread->flatten());
    }

    /**
     * @param ThreadJsonUpdateRequest $request
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function update(ThreadJsonUpdateRequest $request, $id)
    {
        $title = $request->get('title');

        $thread = $this->threadService
                ->updateThreadTitle($id, $title);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        return response()->json($thread->flatten());
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function delete($id)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        $thread->destroy();

        $this->threadDataMapper->flushCache();

        return new JsonResponse(null, 204);
    }
}
