<?php

namespace Railroad\Railforums\Services\Search;

use Railroad\Railforums\DataMappers\PostsSearchIndexDataMapper;

class SearchService
{
    /**
     * @var PostsSearchIndexDataMapper
     */
    protected $postsSearchIndexDataMapper;

    /**
     * SearchService constructor.
     *
     * @param PostsSearchIndexDataMapper $postsSearchIndexDataMapper
     */
    public function __construct(
        PostsSearchIndexDataMapper $postsSearchIndexDataMapper
    ) {
        $this->postsSearchIndexDataMapper = $postsSearchIndexDataMapper;
    }

    /**
     * @param string $term
     * @param int $page
     * @param int $limit
     * @param string $sort
     *
     * @return array
     */
    public function search($term, $page, $limit, $sort)
    {
        return ['results' => [], 'total_results' => 50];
    }
}
