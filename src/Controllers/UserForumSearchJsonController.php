<?php

namespace Railroad\Railforums\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Railforums\Services\Search\SearchService;
use Railroad\Railforums\Responses\JsonPaginatedResponse;

class UserForumSearchJsonController extends Controller
{
    /**
     * @var SearchService
     */
    private $searchService;

    /**
     * @param SearchService $searchService
     */
    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(Request $request)
    {
        $resultData = $this->searchService->search(
            $request->get('term', null),
            $request->get('type', null),
            $request->get('page', 1),
            $request->get('limit', 10),
            $request->get('sort', 'score')
        );

        return new JsonPaginatedResponse(
            $resultData['results'],
            $resultData['total_results'],
            null,
            200
        );
    }
}
