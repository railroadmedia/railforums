<?php

namespace Railroad\Railforums\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\Requests\ThreadCreateRequest;
use Railroad\Railforums\Requests\ThreadUpdateRequest;
use Railroad\Railforums\Services\Threads\UserForumThreadService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumThreadController extends Controller
{
    /**
     * @var UserForumThreadService
     */
    protected $service;

    /**
     * @var UserCloakDataMapper
     */
    protected $userCloakDataMapper;

    /**
     * UserForumThreadController constructor.
     *
     * @param UserForumThreadService $service
     * @param UserCloakDataMapper $userCloakDataMapper
     */
    public function __construct(
        UserForumThreadService $service,
        UserCloakDataMapper $userCloakDataMapper
    ) {
        $this->service = $service;
        $this->userCloakDataMapper = $userCloakDataMapper;
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

        $this->service
            ->createThread($title, $firstPostContent, $categoryId, $authorId);

        $message = ['success' => true];

        return $request->has('redirect') ?
            redirect()->away($request->get('redirect'))->with($message) :
            redirect()->back()->with($message);
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

        $result = $this->service
                ->updateThreadTitle($id, $title);

        if (!$result) {
            throw new NotFoundHttpException();
        }

        $message = ['success' => true];

        return $request->has('redirect') ?
            redirect()->away($request->get('redirect'))->with($message) :
            redirect()->back()->with($message);
    }
}
