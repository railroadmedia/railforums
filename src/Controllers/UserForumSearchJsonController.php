<?php

namespace Railroad\Railforums\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Railforums\Decorators\StripTagDecorator;
use Railroad\Railforums\Repositories\SearchIndexRepository;
use Railroad\Railforums\Responses\JsonPaginatedResponse;

class UserForumSearchJsonController extends Controller
{
    /**
     * @var SearchIndexRepository
     */
    private $searchIndexRepository;
    /**
     * @var StripTagDecorator
     */
    private $stripTagDecorator;

    /**
     * UserForumSearchJsonController constructor.
     * @param SearchIndexRepository $searchIndexRepository
     * @param StripTagDecorator $stripTagDecorator
     */
    public function __construct(SearchIndexRepository $searchIndexRepository, StripTagDecorator $stripTagDecorator)
    {
        $this->searchIndexRepository = $searchIndexRepository;
        $this->stripTagDecorator = $stripTagDecorator;
    }

    public function index(Request $request)
    {
        $results = $this->searchIndexRepository
            ->search(
                $request->get('term', null),
                $request->get('page', 1),
                $request->get('limit', 10),
                $request->get('sort', 'score')
            );

        $count = $this->searchIndexRepository
            ->countTotalResults($request->get('term', null));

        return new JsonPaginatedResponse(
            $this->stripTagDecorator->decorate($results),
            $count,
            null,
            200
        );
    }
}
