<?php

namespace Railroad\Railforums\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Railforums\Repositories\SearchIndexRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserForumSearchJsonController extends Controller
{
    /**
     * @var SearchIndexRepository
     */
    private $searchIndexRepository;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * @param SearchIndexRepository $searchIndexRepository
     * @param PermissionService $permissionService
     */
    public function __construct(
        SearchIndexRepository $searchIndexRepository,
        PermissionService $permissionService
    ) {
        $this->searchIndexRepository = $searchIndexRepository;
        $this->permissionService = $permissionService;
    }

    public function index(Request $request)
    {
        if (!$this->permissionService->can(auth()->id(), 'index-search')) {
            throw new NotFoundHttpException();
        }

        $results = $this->searchIndexRepository
                        ->search(
                            $request->get('term', null),
                            $request->get('type', null),
                            $request->get('page', 1),
                            $request->get('limit', 10),
                            $request->get('sort', 'score')
                        );

        $count = $this->searchIndexRepository
                        ->countTotalResults(
                            $request->get('term', null),
                            $request->get('type', null)
                        );

        return reply()->json($results, ['totalResults' => $count]);
    }
}
