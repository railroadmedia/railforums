<?php

namespace Railroad\Railforums\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Railforums\Repositories\SearchIndexRepository;
use Railroad\Railforums\Responses\JsonPaginatedResponse;

class UserForumSearchJsonController extends Controller
{
    /**
     * @var SearchIndexRepository
     */
    private $searchIndexRepository;

    /**
     * @param SearchIndexRepository $searchIndexRepository
     */
    public function __construct(SearchIndexRepository $searchIndexRepository)
    {
        $this->searchIndexRepository = $searchIndexRepository;
    }

    public function index(Request $request)
    {
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
        // $count = 0;

        return new JsonPaginatedResponse(
            $results,
            $count,
            null,
            200
        );
    }
}
