<?php

namespace Railroad\Railforums\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\Requests\PostCreateRequest;
use Railroad\Railforums\Requests\PostUpdateRequest;
use Railroad\Railforums\Services\Posts\UserForumPostService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumPostController extends Controller
{
    /**
     * @var UserForumPostService
     */
    protected $service;

    /**
     * @var UserCloakDataMapper
     */
    protected $userCloakDataMapper;

    /**
     * UserForumPostController constructor.
     *
     * @param UserForumPostService $service
     * @param UserCloakDataMapper $userCloakDataMapper
     */
    public function __construct(
        UserForumPostService $service,
        UserCloakDataMapper $userCloakDataMapper
    ) {
         // echo "\n\n$$$ __construct\n\n";
        $this->service = $service;
        $this->userCloakDataMapper = $userCloakDataMapper;
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

        $this->service
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

        $result = $this->service
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
