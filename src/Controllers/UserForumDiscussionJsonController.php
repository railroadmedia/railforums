<?php

namespace Railroad\Railforums\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Exceptions\NotAllowedException;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Railforums\Repositories\CategoryRepository;
use Railroad\Railforums\Requests\DiscussionJsonCreateRequest;
use Railroad\Railforums\Requests\DiscussionJsonIndexRequest;
use Railroad\Railforums\Requests\DiscussionJsonUpdateRequest;
use Railroad\Railforums\Responses\JsonPaginatedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumDiscussionJsonController extends Controller
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
     * UserForumDiscussionJsonController constructor.
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
     * @param DiscussionJsonIndexRequest $request
     * @return JsonPaginatedResponse
     * @throws NotAllowedException
     */
    public function index(DiscussionJsonIndexRequest $request)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'index-discussions');

        $discussions =
            $this->categoryRepository->getDecoratedCategories($request->get('amount'), $request->get('page', 1))
                ->toArray();
        $discussionsCount = $this->categoryRepository->getCategoriesCount();

        return new JsonPaginatedResponse(
            $discussions, $discussionsCount, null, 200
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws NotAllowedException
     * @throws \Throwable
     */
    public function show($id)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'show-discussions');

        $discussions = $this->categoryRepository->getDecoratedCategoriesByIds([$id]);
        throw_if(!$discussions || $discussions->isEmpty(), new NotFoundHttpException('Discussion not found'));

        return response()->json($discussions->first());
    }

    /**
     * @param DiscussionJsonCreateRequest $request
     * @return JsonResponse
     * @throws NotAllowedException
     */
    public function store(DiscussionJsonCreateRequest $request)
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
                        'icon',
                    ]
                ),
                [
                    'brand' => config('railforums.brand'),
                    'slug' => CategoryRepository::sanitizeForSlug(
                        $request->get('title')
                    ),
                    'created_at' => $now,
                ]
            )
        );

        $discussions = $this->categoryRepository->getDecoratedCategoriesByIds([$discussion->id]);

        return response()->json($discussions->first());
    }

    /**
     * @param DiscussionJsonUpdateRequest $request
     * @param $id
     * @return JsonResponse
     * @throws NotAllowedException
     * @throws \Throwable
     */
    public function update(DiscussionJsonUpdateRequest $request, $id)
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
                    ['title']
                ),
                [
                    'updated_at' => Carbon::now()
                        ->toDateTimeString(),
                ]
            )
        );

        $discussions = $this->categoryRepository->getDecoratedCategoriesByIds([$discussion->id]);

        return response()->json($discussions->first());
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws NotAllowedException
     * @throws \Throwable
     */
    public function delete($id)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'delete-discussions');

        $discussion = $this->categoryRepository->read($id);
        throw_if(!$discussion, new NotFoundHttpException());

        $result = $this->categoryRepository->delete($id);
        throw_if(!$result, new NotFoundHttpException());

        return new JsonResponse(null, 204);
    }
}
