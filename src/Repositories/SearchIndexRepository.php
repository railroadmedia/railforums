<?php

namespace Railroad\Railforums\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Railroad\Resora\Entities\Entity;
use Railroad\Resora\Queries\CachedQuery;
use Railroad\Resora\Repositories\RepositoryBase;
use Railroad\Railforums\Services\ConfigService;

class SearchIndexRepository extends RepositoryBase
{
    const SEARCH_TYPE_POSTS = 'posts';
    const SEARCH_TYPE_THREADS = 'threads';

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
    ) {
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

        $table = ConfigService::$tableSearchIndexes;

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

            $postsData = $this->postRepository
                            ->getDecoratedPostsByIds(array_keys($postsIds));

            foreach ($postsData as $postStdData) {

                /** @var \stdClass $postStdData */
                $postPosition = $postsIds[$postStdData->id];

                $entity = new Entity();
                $entity->replace((array) $postStdData);

                $results[$postPosition] = $entity;
            }
        }

        if (!empty($threadsIds)) {

            $threadsData = $this->threadRepository
                            ->getDecoratedThreadsByIds(array_keys($threadsIds));

            foreach ($threadsData as $threadStdData) {

                /** @var \stdClass $threadStdData */
                $threadPosition = $threadsIds[$threadStdData->id];

                $entity = new Entity();
                $entity->replace((array) $threadStdData);

                $results[$threadPosition] = $entity;
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
     * Returns newQuery decorated with term and type filters
     *
     * @param string $term
     * @param string $type
     *
     * @return Builder
     */
    protected function getSearchQuery($term, $type)
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
