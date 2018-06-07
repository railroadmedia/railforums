<?php

namespace Railroad\Railforums\DataMappers;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Railroad\Railmap\Entity\EntityBase;
use Railroad\Railforums\Entities\Post;
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

    public function search($term, $type, $page, $limit, $sort)
    {
        $highMultiplier = config('railforums.search.high_value_multiplier');
        $mediumMultiplier = config('railforums.search.medium_value_multiplier');
        $lowMultiplier = config('railforums.search.low_value_multiplier');

        $termsWithPrefix = '+' . implode(' +', explode(' ', $term));

        $scoreSql = <<<SQL
(MATCH (high_value) AGAINST ('$termsWithPrefix' IN BOOLEAN MODE) * $highMultiplier +
MATCH (medium_value) AGAINST ('$term' IN BOOLEAN MODE) * $mediumMultiplier +
MATCH (low_value) AGAINST ('$term' IN BOOLEAN MODE) * $lowMultiplier) as score
SQL;

        $query = $this
            ->baseQuery()
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
            ->orderBy($sort, 'DESC');

        if ($term) {

            $query->where(
                function (Builder $query) use ($term, $termsWithPrefix) {
                    $query
                        ->whereRaw("MATCH (high_value) AGAINST ('$termsWithPrefix' IN BOOLEAN MODE)")
                        ->orWhereRaw("MATCH (medium_value) AGAINST ('$term' IN BOOLEAN MODE)")
                        ->orWhereRaw("MATCH (low_value) AGAINST ('$term' IN BOOLEAN MODE)");
                }
            );
        }

        $this->restrictByType($query, $type);

        // echo "\n\n search query: " . $query->toSql() . "\n\n";

        return $this->getSearchContentResults($query->get());
    }

    public function getSearchContentResults(Collection $results)
    {
        $postsIds = []; // key is post id, value is position in search results
        $threadsIds = []; // key is thread id, value is position in search results

        foreach ($results as $key => $searchIndexStdData) {
            if ($searchIndexStdData->post_id) {
                $postsIds[$searchIndexStdData->post_id] = $key;
            } else {
                $threadsIds[$searchIndexStdData->thread_id] = $key;
            }
        }

        $results = [];

        if (!empty($postsIds)) {
            $posts = $this->postDataMapper
                        ->gettingQuery()
                        ->whereIn(array_keys($postsIds))
                        ->get();

            // TODO - populate results with posts, on position specified in postsIds
        }

        // TODO - finish implementation
    }

    public function countTotalResults($term, $type)
    {
        // TODO - implement it

        return 50;
    }

    public function createSearchIndexes()
    {
        //delete old indexes
        $this->deleteOldIndexes();

        $this->postDataMapper->createSearchIndexes();
        $this->threadDataMapper->createSearchIndexes();

        DB::statement('OPTIMIZE table ' . $this->table);
    }

    /**
     * Adds type restriction logic to query
     *
     * @param Builder $query
     * @param string $type
     */
    protected function restrictByType(Builder $query, $type = '')
    {
        if ($type == self::SEARCH_TYPE_THREADS) {

            $query->whereNull('post_id');

        } else if ($type == self::SEARCH_TYPE_POSTS) {

            $query->whereNotNull('post_id');
        }
    }

    /**
     * Delete old indexes
     */
    protected function deleteOldIndexes()
    {
        $this->baseQuery()->truncate();
    }
}
