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
use Railroad\Railforums\Responses\JsonPaginatedResponse;

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
     */
    public function index(DiscussionJsonIndexRequest $request)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'index-categories');

        $amount = $request->get('limit', 10);
        $page = $request->get('page', 1);

        $categories = $this->categoryRepository->getDecoratedCategories(
                $amount,
                $page
            )
            ->toArray();

        $categoriesCount = $this->categoryRepository->getCategoriesCount();

        return new JsonPaginatedResponse(
            $categories, $categoriesCount, null, 200
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws NotAllowedException
     */
    public function show($id)
    {
         $this->permissionService->canOrThrow(auth()->id(), 'show-categories');

        $categories = $this->categoryRepository->getDecoratedCategoriesByIds([$id]);

        if (!$categories || $categories->isEmpty()) {
            return response()->json('Discussion not found', 404);
        }

        return response()->json($categories->first());
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
                        'weight'
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

        $categories = $this->categoryRepository->getDecoratedCategoriesByIds([$discussion->id]);

        return response()->json($categories->first());
    }

}
