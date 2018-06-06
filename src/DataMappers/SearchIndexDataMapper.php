<?php

namespace Railroad\Railforums\DataMappers;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
    const AUTHOR_KEY_COLUMN = 'author_id';

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

    /**
     * @var string
     */
    protected $authorsTable;

    /**
     * @var string
     */
    protected $authorsTableKey;

    /**
     * @var string
     */
    protected $displayNameColumn;

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

        $this->authorsTable = config('railforums.author_table_name');
        $this->authorsTableKey = config('railforums.author_table_id_column_name');
        $this->displayNameColumn = config('railforums.author_table_display_name_column_name');
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
        // TODO - deal with the search type Posts only, Threads only, Posts + Threads / same must apply to count query

        $termsWithPrefix = '+' . implode(' +', explode(' ', $term));
        $query = $this
            ->baseQuery()
            ->addSelect(
                [
                    $this->table . '.id',
                    $this->table . '.high_value',
                    $this->table . '.medium_value',
                    $this->table . '.low_value',
                    DB::raw(
                        "(MATCH (high_value) AGAINST ('$termsWithPrefix' IN BOOLEAN MODE) * 4 + " .
                        "MATCH (medium_value) AGAINST ('$term' IN BOOLEAN MODE) * 2 + " .
                        "MATCH (low_value) AGAINST ('$term' IN BOOLEAN MODE)) as score"
                    ),
                    DB::raw("MATCH (high_value) AGAINST ('$termsWithPrefix' IN BOOLEAN MODE) * 4 AS high_score"),
                    DB::raw("MATCH (medium_value) AGAINST ('$term' IN BOOLEAN MODE) * 2 AS medium_score"),
                    DB::raw("MATCH (low_value) AGAINST ('$term' IN BOOLEAN MODE) AS low_score"),
                ]
            )
            ->limit($limit)
            ->skip(($page - 1) * $limit)
            ->orderBy($sort, 'DESC');

        echo "\n\n search query: " . $query->toSql() . "\n\n";

        // TODO - get ids from search results and query for Post and/or Threads objects
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

        $this->indexMapper($this->postDataMapper);
        $this->indexMapper($this->threadDataMapper);

        DB::statement('OPTIMIZE table ' . $this->table);
    }

    /**
     * Creates and persists a collection of SearchIndex entities
     * SearchIndex entities are created based on mapper's collection
     *
     * @param DataMapperBase $mapper
     */
    protected function indexMapper(DataMapperBase $mapper)
    {
        /** @var \Illuminate\Database\Query\Builder $query */
        $query = $mapper
                    ->baseQuery()
                    ->select($mapper->table . '.*')
                    ->addSelect($this->authorsTable . '.' . $this->displayNameColumn)
                    ->join(
                        $this->authorsTable,
                        $this->authorsTable . '.' . $this->authorsTableKey,
                        '=',
                        $mapper->table . '.' . self::AUTHOR_KEY_COLUMN
                    )
                    ->orderBy('id');

        $query->chunk(
            self::CHUNK_SIZE,
            function (Collection $results) use ($mapper) {

                foreach ($results as $entityData) {

                    /** @var EntityBase $entity */
                    $entity = $mapper->entity();

                    $mapper->fill($entity, (array) $entityData);

                    $searchIndex = $this->createSearchIndex(
                                        $entity,
                                        $entityData->{$this->displayNameColumn}
                                    );

                    $searchIndex->persist();
                }
            }
        );
    }

    /**
     * @param EntityBase $entity
     * @param string $authorName
     *
     * @return SearchIndex
     */
    protected function createSearchIndex(EntityBase $entity, $authorName)
    {
        $searchIndex = new SearchIndex();

        if ($entity instanceof Post) {
            /** @var Post $entity */
            $searchIndex
                ->setMediumValue($entity->getContent())
                ->setPostId($entity->getId())
                ->setThreadId($entity->getThreadId());
        } else {
            /** @var \Railroad\Railforums\Entities\Thread $entity */
            $searchIndex
                ->setHighValue($entity->getTitle())
                ->setThreadId($entity->getId());
        }

        $searchIndex
            ->setLowValue($authorName)
            ->setCreatedAt(Carbon::now()->toDateTimeString());

        return $searchIndex;
    }

    /**
     * Delete old indexes
     */
    protected function deleteOldIndexes()
    {
        $this->baseQuery()->truncate();
    }
}
