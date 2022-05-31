<?php

namespace Railroad\Railforums\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Railroad\Railforums\Contracts\UserProviderInterface;
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

    /**
     * @var UserProviderInterface
     */
    private $userProvider;


    public function __construct(
        PostRepository $postRepository,
        ThreadRepository $threadRepository,
        UserProviderInterface $userProvider
    ) {
        $this->postRepository = $postRepository;
        $this->threadRepository = $threadRepository;
        $this->userProvider = $userProvider;
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
(MATCH (high_value) AGAINST ('$termsWithPrefix' IN BOOLEAN MODE) * $highMultiplier *  (UNIX_TIMESTAMP(published_on) / 1000000000)+
MATCH (medium_value) AGAINST ('$term' IN BOOLEAN MODE) * $mediumMultiplier *  (UNIX_TIMESTAMP(published_on) / 1000000000)+
MATCH (low_value) AGAINST ('$term' IN BOOLEAN MODE) * $lowMultiplier *  (UNIX_TIMESTAMP(published_on) / 1000000000)) as score
SQL;

        $searchIndexResults = $this
            ->getSearchQuery($term)
            ->addSelect(
                [
                    $table . '.id',
                    $table . '.high_value',
                    $table . '.medium_value',
                    $table . '.low_value',
                    $table . '.published_on as published_on',
                    DB::raw($scoreSql),
                    DB::raw(
                        "MATCH (high_value) AGAINST ('$termsWithPrefix' IN BOOLEAN MODE) * $highMultiplier  *  (UNIX_TIMESTAMP(published_on) / 1000000000) AS high_score"
                    ),
                    DB::raw(
                        "MATCH (medium_value) AGAINST ('$term' IN BOOLEAN MODE) * $mediumMultiplier  *  (UNIX_TIMESTAMP(published_on) / 1000000000) AS medium_score"
                    ),
                    DB::raw(
                        "MATCH (low_value) AGAINST ('$term' IN BOOLEAN MODE) * $lowMultiplier  *  (UNIX_TIMESTAMP(published_on) / 1000000000)  AS low_score"
                    ),
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
            $results[$key]['mobile_app_url'] = url()->route('forums.api.post.jump-to', $searchResult->post_id);
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
    public function createSearchIndexes($brand)
    {
        DB::disableQueryLog();

//        $this->deleteOldIndexes();

        $query = $this->postRepository->newQuery()
            ->from(ConfigService::$tablePosts)
            ->join(
                ConfigService::$tableThreads,
                ConfigService::$tablePosts . '.thread_id',
                '=',
                ConfigService::$tableThreads . '.id'
            )
            ->select(
                ConfigService::$tablePosts . '.content',
                ConfigService::$tablePosts . '.thread_id',
                ConfigService::$tablePosts . '.author_id',
                ConfigService::$tablePosts . '.id',
                ConfigService::$tablePosts . '.published_on'
            )
            ->whereNull(ConfigService::$tablePosts . '.deleted_at')
            ->whereNull(ConfigService::$tableThreads . '.deleted_at')
            ->whereIn(
                ConfigService::$tablePosts . '.state',
                ['published']
            )
            ->orderBy(ConfigService::$tablePosts . '.id');


        $now =
            Carbon::now()
                ->toDateTimeString();

        $query->chunkById(
            500,
            function (Collection $postsData) use (&$count, $now, &$command) {
                $searchIndexes = [];
                $userIds = $postsData->pluck('author_id')
                    ->toArray();
                $userIds = array_unique($userIds);
                $users = $this->userProvider->getUsersByIds($userIds);

                foreach ($postsData as $postData) {
                    $author = $users[$postData->author_id] ?? null;
                    $searchIndexes[] = [
                        'high_value' => substr(
                            utf8_encode($this->postRepository->getFilteredPostContent($postData->content)),
                            0,
                            65535
                        ),
                        'low_value' => $author ? $author->getDisplayName() : '',
                        'thread_id' => $postData->thread_id,
                        'post_id' => $postData->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                        'published_on' => $postData->published_on,
                    ];
                }

                DB::connection(ConfigService::$databaseConnectionName)
                    ->table(ConfigService::$tableSearchIndexes)
                    ->upsert(
                        $searchIndexes,
                        ['thread_id', 'post_id']
                    );
//                usleep(3500000);
            },
            ConfigService::$tablePosts . '.id',
            'id'
        );

        $threadsQuery = $this->threadRepository->newQuery()
            ->from(ConfigService::$tableThreads)
            ->select(ConfigService::$tableThreads . '.*')
            ->whereNull(ConfigService::$tableThreads . '.deleted_at')
            ->orderBy(ConfigService::$tableThreads . '.id');

        $threadsQuery->chunkById(
            500,
            function (Collection $threadsData) use ($now) {
                $searchIndexes = [];
                $userIds = $threadsData->pluck('author_id')
                    ->toArray();
                $userIds = array_unique($userIds);
                $users = $this->userProvider->getUsersByIds($userIds);

                foreach ($threadsData as $threadData) {
                    $author = $users[$threadData->author_id] ?? null;
                    $searchIndexes[] = [
                        'medium_value' => $threadData->title,
                        'low_value' => $author ? $author->getDisplayName() : '',
                        'thread_id' => $threadData->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                        'published_on' => $threadData->published_on,
                    ];
                }

                DB::connection(ConfigService::$databaseConnectionName)
                    ->table(ConfigService::$tableSearchIndexes)
                    ->upsert(
                        $searchIndexes,
                        ['thread_id', 'post_id']
                    );
//                usleep(3500000);
            },
            ConfigService::$tableThreads . '.id',
            'id'
        );

//        DB::statement('OPTIMIZE table '.ConfigService::$tableSearchIndexes);

        return true;
    }

    /**
     * Delete old indexes
     */
    protected function deleteOldIndexes()
    {
        $this->query()->truncate();
    }
}
