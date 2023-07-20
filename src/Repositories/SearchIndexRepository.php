<?php

namespace Railroad\Railforums\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Railroad\Railforums\Contracts\UserProviderInterface;
use Railroad\Railforums\Entities\User;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Resora\Queries\CachedQuery;
use Railroad\Resora\Repositories\RepositoryBase;

class SearchIndexRepository extends RepositoryBase
{
    /**
     * @var PostRepository
     */
    protected PostRepository $postRepository;

    /**
     * @var ThreadRepository
     */
    protected ThreadRepository $threadRepository;

    /**
     * @var UserProviderInterface
     */
    private UserProviderInterface $userProvider;


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
     * @return CachedQuery
     */
    protected function newQuery(): CachedQuery
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
    public function search(string $term, int $page, int $limit, string $sort): array
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
    public function getSearchContentResults(Collection $searchResults): array
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
    public function countTotalResults(string $term): int
    {
        return $this->getSearchQuery($term)->count();
    }

    /**
     * Returns newQuery decorated with term filter
     *
     * @param string $term
     *
     * @return CachedQuery|Builder
     */
    protected function getSearchQuery(string $term): CachedQuery|Builder
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
    protected function getPrefixedTerms(string $term): string
    {
        return $term ? '+' . implode(' +', explode(' ', $term)) : $term;
    }

    /**
     * Rebuild the search indexes table
     * by finding all posts and threads updated since the last rebuild,
     * and updating or creating entries in the table, with the option to
     * truncate the table and rebuild it from scratch
     *
     * @param bool $truncate whether to truncate the tables first, or not
     * @return void
     */
    public function rebuildSearchIndexes(bool $truncate = false): void
    {
        DB::disableQueryLog();

        if ($truncate) {
            $this->deleteOldIndexes();
            $lastUpdate = null;
        } else {
            $lastUpdate = $this->getLastDateTimeSearchIndexesRebuilt();
        }

        $updateAt = Carbon::now()->toDateTimeString();

        $postsQuery = $this->getQueryForPostSearchIndexValues($lastUpdate);
        $threadsQuery = $this->getQueryForThreadSearchIndexValues($lastUpdate);
        $users = $this->getPostAuthors($postsQuery);

        $this->updatePostSearchIndexes($postsQuery, $users, $updateAt);
        $this->updateThreadSearchIndexes($threadsQuery, $users, $updateAt);
    }

    /**
     * Get the datetime when the Search Index table was last rebuilt
     *
     * @return string
     */
    protected function getLastDateTimeSearchIndexesRebuilt(): string
    {
        // figure out when the last update was done, so we can scope the query
        $lastUpdate = DB::connection(ConfigService::$databaseConnectionName)
            ->table(ConfigService::$tableSearchIndexes)
            ->select("updated_at")
            ->orderByDesc("updated_at")
            ->limit(1)
            ->value("updated_at");

        // safety in case of first run
        if (is_null($lastUpdate)) {
            $lastUpdate = Carbon::createFromTimestamp(0)->toDateTimeString();
        }

        return $lastUpdate;
    }

    /**
     * Build up the query to get the data required for Thread search index entries
     *
     * @param Carbon|string|null $lastUpdate
     * @return ThreadRepository|CachedQuery
     */
    protected function getQueryForThreadSearchIndexValues(Carbon|string|null $lastUpdate): ThreadRepository|CachedQuery
    {
        return $this->threadRepository->newQuery()
            ->from(ConfigService::$tableThreads)
            ->select(ConfigService::$tableThreads . '.id',
                ConfigService::$tableThreads . '.title',
                ConfigService::$tableThreads . '.author_id',
                ConfigService::$tableThreads . '.published_on'
            )
            ->whereNull(ConfigService::$tableThreads . '.deleted_at')
            ->when(!is_null($lastUpdate), fn ($q) =>
                $q->where(ConfigService::$tableThreads . ".updated_at", ">=", $lastUpdate)
            )
            ->orderBy(ConfigService::$tableThreads . '.id');
    }

    /**
     * Build up the query to get the data required for Post search index entries
     *
     * @param Carbon|string|null $lastUpdate
     * @return PostRepository|CachedQuery
     */
    protected function getQueryForPostSearchIndexValues(Carbon|string|null $lastUpdate): PostRepository|CachedQuery
    {
        return $this->postRepository->newQuery()
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
            ->when(!is_null($lastUpdate), fn ($q) =>
                $q->where(ConfigService::$tablePosts . ".updated_at", ">=", $lastUpdate)
            )
            ->whereNull(ConfigService::$tablePosts . '.deleted_at')
            ->whereNull(ConfigService::$tableThreads . '.deleted_at')
            ->whereIn(
                ConfigService::$tablePosts . '.state',
                ['published']
            )
            ->orderBy(ConfigService::$tablePosts . '.id');
    }

    /**
     * Update the search index table with entries for Threads
     *
     * @param PostRepository|CachedQuery $query
     * @param User[] $users
     * @param string $updateAt
     * @return void
     */
    protected function updateThreadSearchIndexes(PostRepository|CachedQuery $query, array $users, string $updateAt): void
    {
        $query->chunkById(
            2000,
            function (Collection $threadsData) use ($updateAt, $users) {
                $searchIndexes = [];
                foreach ($threadsData as $threadData) {
                    $author = $users[$threadData->author_id] ?? null;
                    $searchIndexes[] = [
                        'medium_value' => $threadData->title,
                        'low_value' => $author ? $author->getDisplayName() : '',
                        'thread_id' => $threadData->id,
                        'created_at' => $updateAt,
                        'updated_at' => $updateAt,
                        'published_on' => $threadData->published_on,
                    ];
                }

                // perform the updateOrInsert inside a transaction, so we can push all of these in one transaction
                // instead of causing A LOT of connections
                DB::transaction(function () use ($searchIndexes) {
                    foreach ($searchIndexes as $searchIndex) {
                        DB::connection(ConfigService::$databaseConnectionName)
                            ->table(ConfigService::$tableSearchIndexes)
                            ->updateOrInsert(
                                ["thread_id" => $searchIndex["thread_id"], "post_id" => null],
                                $searchIndex
                            );
                    }
                });

                usleep(250000); //delay 250 ms to reduce load
            },
            ConfigService::$tableThreads . '.id',
            'id'
        );
    }

    /**
     * Update the search index table with entries for Posts
     *
     * @param PostRepository|CachedQuery $query
     * @param User[] $users
     * @param string $updateAt
     * @return void
     */
    protected function updatePostSearchIndexes(PostRepository|CachedQuery $query, array $users, string $updateAt): void
    {
        $query->chunkById(
            2000,
            function (Collection $postsData) use ($updateAt, $users) {
                $searchIndexes = [];
                foreach ($postsData as $postData) {
                    $author = $users[$postData->author_id] ?? null;

                    $searchIndexes[] = [
                        'high_value' => substr(
                            utf8_encode($this->postRepository->getFilteredPostContent($postData->content)),
                            0,
                            65535
                        ),
                        'low_value' => $author?->getDisplayName() ?? '',
                        'thread_id' => $postData->thread_id,
                        'post_id' => $postData->id,
                        'created_at' => $updateAt,
                        'updated_at' => $updateAt,
                        'published_on' => $postData->published_on,
                    ];
                }

                DB::connection(ConfigService::$databaseConnectionName)
                    ->table(ConfigService::$tableSearchIndexes)
                    ->upsert(
                        $searchIndexes,
                        ['thread_id', 'post_id']
                    );
                usleep(250000); //delay 250 ms to reduce load
            },
            ConfigService::$tablePosts . '.id',
            'id'
        );
    }

    /**
     * Get an array of the User models for all unique Post authors
     *
     * @param PostRepository|CachedQuery $postsQuery
     * @return User[]
     */
    protected function getPostAuthors(PostRepository|CachedQuery $postsQuery): array
    {
        $key = "author_id";
        $column = ConfigService::$tablePosts . ".$key";
        $usersQuery = $postsQuery->cloneWithout(["orders"]);

        $userIds = $usersQuery
            ->select($column)
            ->orderBy($column)
            ->distinct()
            ->get()
            ->pluck($key);

        $users = collect();
        foreach ($userIds->chunk(1000) as $chunk) {
            $users->push($this->userProvider->getUsersByIds($chunk->all()));
        }
        return $users->flatten()->toArray();
    }

    /**
     * Delete old indexes
     */
    protected function deleteOldIndexes(): void
    {
        $this->query()->truncate();
    }
}
