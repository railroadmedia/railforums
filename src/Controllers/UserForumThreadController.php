<?php

namespace Railroad\Railforums\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\Requests\ThreadCreateRequest;
use Railroad\Railforums\Requests\ThreadUpdateRequest;
use Railroad\Railforums\Services\Posts\ForumThreadReadService;
use Railroad\Railforums\Services\ThreadFollows\ThreadFollowService;
use Railroad\Railforums\Services\Threads\UserForumThreadService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumThreadController extends Controller
{
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
     * @var ThreadDataMapper
     */
    protected $threadDataMapper;

    /**
     * @var UserCloakDataMapper
     */
    protected $userCloakDataMapper;

    /**
     * UserForumThreadController constructor.
     *
     * @param ForumThreadReadService $threadReadService
     * @param ThreadFollowService $threadFollowService
     * @param UserForumThreadService $threadService
     * @param ThreadDataMapper $threadDataMapper
     * @param UserCloakDataMapper $userCloakDataMapper
     */
    public function __construct(
        ForumThreadReadService $threadReadService,
        ThreadFollowService $threadFollowService,
        UserForumThreadService $threadService,
        ThreadDataMapper $threadDataMapper,
        UserCloakDataMapper $userCloakDataMapper
    ) {
        $this->threadReadService = $threadReadService;
        $this->threadFollowService = $threadFollowService;
        $this->threadService = $threadService;
        $this->threadDataMapper = $threadDataMapper;
        $this->userCloakDataMapper = $userCloakDataMapper;

        $this->middleware(config('railforums.controller_middleware'));
    }

    /**
     * @param Request $request
     * @param integer $id
     *
     * @return RedirectResponse
     */
    public function read(Request $request, $id)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        $this->threadReadService->markThreadRead(
            $thread->getId(),
            $this->userCloakDataMapper->getCurrentId()
        );

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
    public function follow(Request $request, $id)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!$thread) {
            throw new NotFoundHttpException();
        }

        $this->threadFollowService->follow(
            $thread->getId(),
            $this->userCloakDataMapper->getCurrentId()
        );

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
    public function unfollow(Request $request, $id)
    {
        $this->threadFollowService->unFollow(
            $id,
            $this->userCloakDataMapper->getCurrentId()
        );

        $message = ['success' => true];

        return $request->has('redirect') ?
            redirect()->away($request->get('redirect'))->with($message) :
            redirect()->back()->with($message);
    }

    /**
     * @param ThreadCreateRequest $request
     *
     * @return RedirectResponse
     */
    public function store(ThreadCreateRequest $request)
    {
        $title = $request->get('title');
        $firstPostContent = $request->get('first_post_content');
        $categoryId = $request->get('category_id');
        $authorId = $this->userCloakDataMapper->getCurrentId();

        $thread = $this->threadService
            ->createThread($title, $firstPostContent, $categoryId, $authorId);

        $message = ['success' => true];

        // todo: temporary
        return redirect()->to('/members/forums/thread/' . $thread->getId())->with($message);

        //        return $request->has('redirect') ?
        //            redirect()->away($request->get('redirect'))->with($message) :
        //            redirect()->back()->with($message);
    }

    /**
     * @param ThreadUpdateRequest $request
     * @param integer $id
     *
     * @return RedirectResponse
     */
    public function update(ThreadUpdateRequest $request, $id)
    {
        $title = $request->get('title');

        $result = $this->threadService
            ->updateThread(
                $id,
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
                )
            );

        if (!$result) {
            throw new NotFoundHttpException();
        }

        $message = ['success' => true];

        return $request->has('redirect') ?
            redirect()->away($request->get('redirect'))->with($message) :
            redirect()->back()->with($message);
    }
}
