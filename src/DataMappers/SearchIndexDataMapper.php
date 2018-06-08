<?php

namespace Railroad\Railforums\DataMappers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Railroad\Railforums\Entities\SearchIndex;

/**
 * Class SearchIndexDataMapper
 *
 */
class SearchIndexDataMapper extends DataMapperBase
{
    const CHUNK_SIZE = 100;
    const SEARCH_TYPE_POSTS = 'posts';
    const SEARCH_TYPE_THREADS = 'threads';

    /**
     * @var string
     */
    public $table = 'forum_search_indexes';

    /**
     * @var PostDataMapper
     */
    protected $postDataMapper;

    /**
     * @var ThreadDataMapper
     */
    protected $threadDataMapper;

    public function mapTo()
    {
        return [
            'id' => 'id',
            'highValue' => 'high_value',
            'mediumValue' => 'medium_value',
            'lowValue' => 'low_value',
            'postId' => 'post_id',
            'threadId' => 'thread_id',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at'
        ];
    }

    public function __construct(
        PostDataMapper $postDataMapper,
        ThreadDataMapper $threadDataMapper
    ) {
        parent::__construct();

        $this->postDataMapper = $postDataMapper;
        $this->threadDataMapper = $threadDataMapper;
    }

    /**
     * @return SearchIndex
     */
    public function entity()
    {
        return new SearchIndex();
    }

    /**
     * Returns a page of matching results
     * Based on type param, results may be a mix of posts and/or threads
     *
     * @param string $term
     * @param string $type - 'posts' | 'threads' | null
     * @param int $page
     * @param int $limit
     * @param string $sort
     *
     * @return array
     */
    public function search($term, $type, $page, $limit, $sort)
    {
        $highMultiplier = config('railforums.search.high_value_multiplier');
        $mediumMultiplier = config('railforums.search.medium_value_multiplier');
        $lowMultiplier = config('railforums.search.low_value_multiplier');

        $termsWithPrefix = $this->getPrefixedTerms($term);

        $scoreSql = <<<SQL
(MATCH (high_value) AGAINST ('$termsWithPrefix' IN BOOLEAN MODE) * $highMultiplier +
MATCH (medium_value) AGAINST ('$term' IN BOOLEAN MODE) * $mediumMultiplier +
MATCH (low_value) AGAINST ('$term' IN BOOLEAN MODE) * $lowMultiplier) as score
SQL;

        $searchIndexResults = $this
            ->getSearchQuery($term, $type)
            ->addSelect(
                [
                    $this->table . '.id',
                    $this->table . '.high_value',
                    $this->table . '.medium_value',
                    $this->table . '.low_value',
                    DB::raw($scoreSql),
                    DB::raw("MATCH (high_value) AGAINST ('$termsWithPrefix' IN BOOLEAN MODE) * $highMultiplier AS high_score"),
                    DB::raw("MATCH (medium_value) AGAINST ('$term' IN BOOLEAN MODE) * $mediumMultiplier AS medium_score"),
                    DB::raw("MATCH (low_value) AGAINST ('$term' IN BOOLEAN MODE) * $lowMultiplier AS low_score"),
                    $this->table . '.post_id',
                    $this->table . '.thread_id',
                ]
            )
            ->limit($limit)
            ->skip(($page - 1) * $limit)
            ->orderBy($sort, 'DESC')
            ->get();

        return $this->getSearchContentResults($searchIndexResults);
    }

    /**
     * Assembles the search results array or posts and/od threads
     * using the search indexes results collection
     *
     * @param Collection $searchResults
     *
     * @return array
     */
    public function getSearchContentResults(Collection $searchResults)
    {
        $postsIds = []; // key is post id, value is position in searchResults
        $threadsIds = []; // key is thread id, value is position in searchResults

        foreach ($searchResults as $key => $searchIndexStdData) {
            if ($searchIndexStdData->post_id) {
                $postsIds[$searchIndexStdData->post_id] = $key;
            } else {
                $threadsIds[$searchIndexStdData->thread_id] = $key;
            }
        }

        // pre-fill the results array to insert content in correct order
        $results = array_fill(0, count($searchResults), null);

        if (!empty($postsIds)) {

            $postsData = $this->postDataMapper
                            ->gettingQuery()
                            ->whereIn('id', array_keys($postsIds))
                            ->get();

            foreach ($postsData as $postStdData) {

                /** @var \stdClass $postStdData */
                $postPosition = $postsIds[$postStdData->id];

                $results[$postPosition] = (array) $postStdData;
            }
        }

        if (!empty($threadsIds)) {

            $threadsData = $this->threadDataMapper
                            ->gettingQuery()
                            ->whereIn('id', array_keys($threadsIds))
                            ->get();

            foreach ($threadsData as $threadStdData) {

                /** @var \stdClass $threadStdData */
                $threadPosition = $threadsIds[$threadStdData->id];

                $results[$threadPosition] = (array) $threadStdData;
            }
        }

        return $results;
    }

    /**
     * Returns the number of search index records that match term and type
     *
     * @param string $term
     * @param string $type
     *
     * @return int
     */
    public function countTotalResults($term, $type)
    {
        return $this->getSearchQuery($term, $type)->count();
    }

    /**
     * Truncates search indexes table
     * Calls post and thread data mappers createSearchIndexes method
     * Calls SQL optimize command
     *
     * @return void
     */
    public function createSearchIndexes()
    {
        //delete old indexes
        $this->deleteOldIndexes();

        $this->postDataMapper->createSearchIndexes();
        $this->threadDataMapper->createSearchIndexes();

        DB::statement('OPTIMIZE table ' . $this->table);
    }

    /**
     * Returns baseQuery decorated with term and type filters
     *
     * @param string $term
     * @param string $type
     *
     * @return Builder
     */
    protected function getSearchQuery($term, $type)
    {
        $query = $this->baseQuery();

        if ($term) {

            $termsWithPrefix = $this->getPrefixedTerms($term);

            $query->where(
                function (Builder $query) use ($term, $termsWithPrefix) {
                    $query
                        ->whereRaw("MATCH (high_value) AGAINST ('$termsWithPrefix' IN BOOLEAN MODE)")
                        ->orWhereRaw("MATCH (medium_value) AGAINST ('$term' IN BOOLEAN MODE)")
                        ->orWhereRaw("MATCH (low_value) AGAINST ('$term' IN BOOLEAN MODE)");
                }
            );
        }

        if ($type == self::SEARCH_TYPE_THREADS) {

            $query->whereNull('post_id');

        } else if ($type == self::SEARCH_TYPE_POSTS) {

            $query->whereNotNull('post_id');
        }

        return $query;
    }

    /**
     * Returns a string containing all words from $term prefixed with '+'
     *
     * @param string $term
     *
     * @return string
     */
    protected function getPrefixedTerms($term)
    {
        return $term ? '+' . implode(' +', explode(' ', $term)) : $term;
    }

    /**
     * Delete old indexes
     */
    protected function deleteOldIndexes()
    {
        $this->baseQuery()->truncate();
    }
}
