<?php

namespace Railroad\Railforums\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotAllowedException;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Railforums\Repositories\CategoryRepository;
use Railroad\Railforums\Requests\DiscussionCreateRequest;
use Railroad\Railforums\Requests\DiscussionJsonCreateRequest;
use Railroad\Railforums\Requests\DiscussionJsonIndexRequest;
use Railroad\Railforums\Requests\DiscussionJsonUpdateRequest;
use Railroad\Railforums\Requests\DiscussionUpdateRequest;
use Railroad\Railforums\Responses\JsonPaginatedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumDiscussionController extends Controller
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var PermissionService
     */
    private $permissionService;

    /**
     * UserForumDiscussionController constructor.
     *
     * @param PermissionService $permissionService
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        PermissionService $permissionService,
        CategoryRepository $categoryRepository
    ) {
        $this->permissionService = $permissionService;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param DiscussionCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws NotAllowedException
     */
    public function store(DiscussionCreateRequest $request)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'create-discussions');

        $now =
            Carbon::now()
                ->toDateTimeString();

        $discussion = $this->categoryRepository->create(
            array_merge(
                $request->only(
                    [
                        'title',
                        'description',
                        'weight',
                        'topic',
                    ]
                ),
                [
                    'brand' => config('railforums.brand'),
                    'slug' => CategoryRepository::sanitizeForSlug(
                        $request->get('title')
                    ),
                    'topic' => config('railforums.topics')[$request->get('topic')] ?? null,
                    'created_at' => $now,
                ]
            )
        );

        $message = ['success' => true];

        return redirect()->to('/members/forums')->with($message);
    }

    /**
     * @param DiscussionUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update(DiscussionUpdateRequest $request, $id)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'update-discussions');

        $discussion = $this->categoryRepository->read($id);
        throw_if(!$discussion, new NotFoundHttpException());

        $discussion = $this->categoryRepository->update(
            $id,
            array_merge(
                $this->permissionService->columns(
                    auth()->id(),
                    'update-discussions',
                    $request->all(),
                    ['title', 'description', 'topic']
                ),
                [
                    'updated_at' => Carbon::now()
                        ->toDateTimeString(),
                ]
            )
        );

        $message = ['success' => true];

        return redirect()->to('/members/forums')->with($message);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws NotAllowedException
     * @throws \Throwable
     */
    public function delete(Request $request, $id)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'delete-discussions');

        $discussion = $this->categoryRepository->read($id);
        throw_if(!$discussion, new NotFoundHttpException());

        $result = $this->categoryRepository->delete($id);
        throw_if(!$result, new NotFoundHttpException());

        $message = ['success' => true];

        return $request->has('redirect') ?
            redirect()
                ->away($request->get('redirect'))
                ->with($message) :
            redirect()
                ->back()
                ->with($message);
    }
}
