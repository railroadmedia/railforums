<?php

namespace Railroad\Railforums\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railforums\Requests\ThreadJsonIndexRequest;
use Railroad\Railforums\Requests\ThreadJsonCreateRequest;
use Railroad\Railforums\Requests\ThreadJsonUpdateRequest;
use Railroad\Railforums\Services\Threads\UserForumThreadService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumThreadJsonController extends Controller
{
    const AMOUNT = 10;
    const PAGE = 1;

    /**
     * @var UserForumThreadService
     */
    protected $service;

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
     * @param UserForumThreadService $service
     * @param UserCloakDataMapper $userCloakDataMapper
     * @param ThreadDataMapper $threadDataMapper
     */
    public function __construct(
        UserForumThreadService $service,
        UserCloakDataMapper $userCloakDataMapper,
        ThreadDataMapper $threadDataMapper
    ) {
        $this->service = $service;
        $this->userCloakDataMapper = $userCloakDataMapper;
        $this->threadDataMapper = $threadDataMapper;
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
        $categoryId = (int) $request->get('category_id');
        $pinned = (boolean) $request->get('pinned');
        $followed = $request->has('followed') ?
            (boolean) $request->get('followed') : null;

        $threads = $this->service
            ->getThreads($amount, $page, $categoryId, $pinned, $followed);

        $response = [];

        foreach ($threads as $thread) {
            $response[] = $thread->flatten();
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

        $thread = $this->service
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

        $thread = $this->service
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
