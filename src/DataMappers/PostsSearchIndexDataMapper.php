<?php

namespace Railroad\Railforums\DataMappers;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Railroad\Railforums\Entities\PostsSearchIndex;

/**
 * Class PostsSearchIndexDataMapper
 *
 */
class PostsSearchIndexDataMapper extends DataMapperBase
{
    const CHUNK_SIZE = 100;

    /**
     * @var string
     */
    public $table = 'forum_posts_search_indexes';

    /**
     * @var PostDataMapper
     */
    protected $postDataMapper;

    public function mapTo()
    {
        return [
            'id' => 'id',
            'highValue' => 'high_value',
            'mediumValue' => 'medium_value',
            'lowValue' => 'low_value',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at'
        ];
    }

    public function __construct(PostDataMapper $postDataMapper)
    {
        parent::__construct();

        $this->postDataMapper = $postDataMapper;
    }

    /**
     * @return PostsSearchIndex
     */
    public function entity()
    {
        return new PostsSearchIndex();
    }

    public function createSearchIndexes()
    {
        //delete old indexes
        $this->deleteOldIndexes();

        $query = $this->getPostsQuery()
            ->orderBy('id');

        $query->chunk(
            self::CHUNK_SIZE,
            function ($query) {

                foreach ($query as $post) {

                    // echo "\n\npost: " . var_export($post) . "\n\n";

                    $postsSearchIndex = new PostsSearchIndex();

                    $postsSearchIndex->setHighValue(
                        $this->prepareIndexesValues('high_value', $post)
                    );

                    $postsSearchIndex->setMediumValue(
                        $this->prepareIndexesValues('medium_value', $post)
                    );

                    $postsSearchIndex->setLowValue(
                        $this->prepareIndexesValues('low_value', $post)
                    );

                    $postsSearchIndex->setCreatedAt(Carbon::now()->toDateTimeString());

                    $postsSearchIndex->persist();
                }
            }
        );

        DB::statement('OPTIMIZE table ' . $this->table);
    }

    /**
     * @return Builder
     */
    protected function getPostsQuery()
    {
        return $this->postDataMapper->baseQuery();
    }

    /**
     * @param string $type
     * @param object $post
     *
     * @return string
     */
    protected function prepareIndexesValues($type, $post)
    {
        // TODO - implement it
        return $type . ' ' . $post->content;
    }

    /**
     * Delete old indexes
     */
    protected function deleteOldIndexes()
    {
        $this->baseQuery()->truncate();
    }
}
