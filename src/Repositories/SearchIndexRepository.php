<?php

namespace Railroad\Railforums\Repositories;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Resora\Queries\CachedQuery;
use Railroad\Resora\Repositories\RepositoryBase;

class SearchIndexRepository extends RepositoryBase
{
    const SEARCH_TYPE_POSTS = 'posts';
    const SEARCH_TYPE_THREADS = 'threads';
    const SEARCH_TYPE_FOLLOWED_THREADS = 'followed';

    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * @var ThreadRepository
     */
    protected $threadRepository;


    public function __construct(
        PostRepository $postRepository,
        ThreadRepository $threadRepository
    )
    {
        $this->postRepository = $postRepository;
        $this->threadRepository = $threadRepository;
    }

    /**
     * @return CachedQuery|$this
     */
    protected function newQuery()
    {
        return (new CachedQuery($this->connection()))->from(ConfigService::$tableSearchIndexes);
    }

    protected function connection()
    {
        return app('db')->connection(ConfigService::$databaseConnectionName);
    }

    /**
     * Returns a page of matching results
     *
     * @param string $term
     * @param int $page
     * @param int $limit
     * @param string $sort
     *
     * @return array
     */
    public function search($term, $page, $limit, $sort)
    {
        $highMultiplier = config('railforums.search.high_value_multiplier');
        $mediumMultiplier = config('railforums.search.medium_value_multiplier');
        $lowMultiplier = config('railforums.search.low_value_multiplier');

        $table = ConfigService::$tableSearchIndexes;

        $termsWithPrefix = $this->getPrefixedTerms($term);

        $scoreSql = <<<SQL
(MATCH (high_value) AGAINST ('$termsWithPrefix' IN BOOLEAN MODE) * $highMultiplier +
MATCH (medium_value) AGAINST ('$term' IN BOOLEAN MODE) * $mediumMultiplier +
MATCH (low_value) AGAINST ('$term' IN BOOLEAN MODE) * $lowMultiplier) as score
SQL;

        $searchIndexResults = $this
            ->getSearchQuery($term)
            ->addSelect(
                [
                    $table . '.id',
                    $table . '.high_value',
                    $table . '.medium_value',
                    $table . '.low_value',
                    DB::raw($scoreSql),
                    DB::raw("MATCH (high_value) AGAINST ('$termsWithPrefix' IN BOOLEAN MODE) * $highMultiplier AS high_score"),
                    DB::raw("MATCH (medium_value) AGAINST ('$term' IN BOOLEAN MODE) * $mediumMultiplier AS medium_score"),
                    DB::raw("MATCH (low_value) AGAINST ('$term' IN BOOLEAN MODE) * $lowMultiplier AS low_score"),
                    $table . '.post_id',
                    $table . '.thread_id',
                ]
            )
            ->limit($limit)
            ->skip(($page - 1) * $limit)
            ->orderBy($sort, 'DESC')
            ->get();

        return $this->getSearchContentResults($searchIndexResults);
    }

    /**
     * Assembles the search results array of posts and/or threads
     * using the search indexes results collection
     *
     * @param Collection $searchResults
     *
     * @return array
     */
    public function getSearchContentResults(Collection $searchResults)
    {
        $postsIds = []; // key is post id, value is position in searchResults
        $threadsIds = []; // key is thread id, value is an array with positions of the posts in searchResults

        foreach ($searchResults as $key => $searchIndexStdData) {

            $postsIds[$searchIndexStdData->post_id] = $key;

            // this handles several posts with same thread id
            if (isset($threadsIds[$searchIndexStdData->thread_id])) {
                $threadsIds[$searchIndexStdData->thread_id][] = $key;
            } else {
                $threadsIds[$searchIndexStdData->thread_id] = [$key];
            }
        }

        $postsData = $this->postRepository
            ->getDecoratedPostsByIds(array_keys($postsIds))->keyBy('id');
       
	foreach ($postsData as $postsDatum) {
            $postsDatum['content'] = $this->postRepository->getFilteredPostContent($postsDatum['content']);
        }

        $threadsData = $this->threadRepository
            ->getDecoratedThreadsByIds(array_keys($threadsIds))->keyBy('id');

        $results = [];
        foreach ($searchResults as $key => $searchResult) {
            $results[$key] = $postsData[$searchResult->post_id];
            $results[$key]['mobile_app_url'] = url()->route('forums.api.post.jump-to',$searchResult->post_id);
            $results[$key]['thread'] = $threadsData[$searchResult->thread_id];
        }

        return $results;
    }

    /**
     * Returns the number of search index records that match term
     *
     * @param string $term
     *
     * @return int
     */
    public function countTotalResults($term)
    {
        return $this->getSearchQuery($term)->count();
    }

    /**
     * Returns newQuery decorated with term filter
     *
     * @param string $term
     *
     * @return Builder
     */
    protected function getSearchQuery($term)
    {
        $query = $this->newQuery();

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

        $query->whereNotNull('post_id');

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
     * Truncates search indexes table
     * Calls post and thread repositories createSearchIndexes method
     * Calls SQL optimize command
     *
     * @return void
     */
    public function createSearchIndexes()
    {
        //delete old indexes
        $this->deleteOldIndexes();

        $this->postRepository->createSearchIndexes();

        $this->threadRepository->createSearchIndexes();

        DB::statement('OPTIMIZE table ' . ConfigService::$tableSearchIndexes);
    }

    /**
     * Delete old indexes
     */
    protected function deleteOldIndexes()
    {
        $this->query()->truncate();
    }
}
