<?php

namespace Railroad\Railforums\Services\Search;

use Railroad\Railforums\DataMappers\SearchIndexDataMapper;

class SearchService
{
    /**
     * @var SearchIndexDataMapper
     */
    protected $searchIndexDataMapper;

    /**
     * SearchService constructor.
     *
     * @param SearchIndexDataMapper $searchIndexDataMapper
     */
    public function __construct(
        SearchIndexDataMapper $searchIndexDataMapper
    ) {
        $this->searchIndexDataMapper = $searchIndexDataMapper;
    }

    /**
     * @param string $term
     * @param string $type
     * @param int $page
     * @param int $limit
     * @param string $sort
     *
     * @return array
     */
    public function search($term, $type, $page, $limit, $sort)
    {
        $results = $this->searchIndexDataMapper
                        ->search($term, $type, $page, $limit, $sort);

        $count = $this->searchIndexDataMapper
                        ->countTotalResults($term, $type);

        return ['results' => $results, 'total_results' => $count];
    }
}
