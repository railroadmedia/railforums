<?php

namespace Railroad\Railforums\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\Decorators\PostUserDecorator;
use Railroad\Railforums\Decorators\ThreadUserDecorator;
use Railroad\Railforums\Events\PostCreated;
use Railroad\Railforums\Events\PostDeleted;
use Railroad\Railforums\Events\PostUpdated;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Resora\Collections\BaseCollection;
use Railroad\Resora\Decorators\Decorator;
use Railroad\Resora\Entities\Entity;
use Railroad\Resora\Queries\BaseQuery;
use Railroad\Resora\Queries\CachedQuery;
use Symfony\Component\DomCrawler\Crawler;

class PostRepository extends EventDispatchingRepository
{
    const STATE_PUBLISHED = 'published';
    const ACCESSIBLE_STATES = [self::STATE_PUBLISHED];
    const CHUNK_SIZE = 100;

    /**
     * @var UserCloakDataMapper
     */
    protected $userCloakDataMapper;
    /**
     * @var PostUserDecorator
     */
    private $postUserDecorator;
    /**
     * @var ThreadUserDecorator
     */
    private $threadUserDecorator;

    /**
     * PostRepository constructor.
     *
     * @param PostUserDecorator $postUserDecorator
     * @param ThreadUserDecorator $threadUserDecorator
     */
    public function __construct(PostUserDecorator $postUserDecorator, ThreadUserDecorator $threadUserDecorator)
    {
        $this->userCloakDataMapper = app(UserCloakDataMapper::class);
        $this->postUserDecorator = $postUserDecorator;
        $this->threadUserDecorator = $threadUserDecorator;
    }

    public function getCreateEvent($entity)
    {
        return new PostCreated($entity->id, $this->userCloakDataMapper->getCurrentId());
    }

    public function getReadEvent($entity)
    {
        return null;
    }

    public function getUpdateEvent($entity)
    {
        $id = is_object($entity) ? $entity->id : $entity;

        return new PostUpdated($id, $this->userCloakDataMapper->getCurrentId());
    }

    public function getDestroyEvent($entity)
    {
        return null;
    }

    public function getDeleteEvent($id)
    {
        return new PostDeleted($id, $this->userCloakDataMapper->getCurrentId());
    }

    /**
     * @return CachedQuery|$this
     */
    protected function newQuery()
    {
        return (new CachedQuery($this->connection()))->from(ConfigService::$tablePosts);
    }

    protected function decorate($results)
    {
        return Decorator::decorate($results, 'posts');
    }

    protected function connection()
    {
        return app('db')->connection(ConfigService::$databaseConnectionName);
    }

    protected function baseQuery()
    {
        return new BaseQuery($this->connection());
    }

    /**
     * Returns the posts count of the specified thread
     *
     * @param int $threadId
     *
     * @return int
     */
    public function getPostsCount($threadId)
    {
        return $this->query()
            ->selectRaw('COUNT(' . ConfigService::$tablePosts . '.id) as count')
            ->where(ConfigService::$tablePosts . '.thread_id', $threadId)
            ->whereIn(
                ConfigService::$tablePosts . '.state',
                self::ACCESSIBLE_STATES
            )
            ->whereNull(ConfigService::$tablePosts . '.deleted_at')
            ->value('count');
    }

    /**
     * Returns the posts and associated data
     *
     * @param int $amount
     * @param int $page
     * @param int $threadId
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDecoratedPosts($amount, $page, $threadId)
    {
        return $this->getDecoratedQuery()
            ->where(ConfigService::$tablePosts . '.thread_id', $threadId)
            ->whereIn(
                ConfigService::$tablePosts . '.state',
                self::ACCESSIBLE_STATES
            )
            ->limit($amount)
            ->skip($amount * ($page - 1))
            ->orderBy('published_on', 'asc')
            ->get();
    }

    /**
     * Returns the posts and associated data
     *
     * @param array $ids
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDecoratedPostsByIds($ids)
    {
        return $this->getDecoratedQuery()
            ->whereIn(ConfigService::$tablePosts . '.id', $ids)
            ->get();
    }

    /**
     * Returns the posts of the specified thread
     *
     * @param int $id
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllPostIdsInThread($id)
    {
        return $this->query()
            ->where('thread_id', $id)
            ->whereNull(ConfigService::$tablePosts . '.deleted_at')
            ->whereIn(
                ConfigService::$tablePosts . '.state',
                self::ACCESSIBLE_STATES
            )
            ->orderBy('published_on', 'asc')
            ->get();
    }

    /**
     * Returns a decorated query to retrive posts and associated data
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getDecoratedQuery()
    {
        return $this->query()
            ->select(ConfigService::$tablePosts . '.*')//            ->selectSub(
            //                function (Builder $builder) {
            //                    return $builder->select([
            //                            config('railforums.author_table_display_name_column_name')
            //                        ])
            //                        ->from(config('railforums.author_table_name'))
            //                        ->whereRaw(config('railforums.author_table_name') . '.id = author_id')
            //                        ->limit(1);
            //                },
            //                'author_display_name'
            //            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('COUNT(*)')
                        ->from(ConfigService::$tablePostLikes)
                        ->limit(1)
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.post_id = ' . ConfigService::$tablePosts . '.id'
                        );
                },
                'like_count'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('COUNT(*) > 0')
                        ->from(ConfigService::$tablePostLikes)
                        ->limit(1)
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.post_id = ' . ConfigService::$tablePosts . '.id'
                        )
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.liker_id = ' . $this->userCloakDataMapper->getCurrentId()
                        );
                },
                'is_liked_by_viewer'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('liker_id')
                        ->from(ConfigService::$tablePostLikes)
                        ->limit(1)
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.post_id = ' . ConfigService::$tablePosts . '.id'
                        )
                        ->orderBy('liked_on', 'desc');
                },
                'liker_1_id'
            )//            ->selectSub(
            //                function (Builder $builder) {
            //                    return $builder->select([
            //                            config('railforums.author_table_display_name_column_name')
            //                        ])
            //                        ->from(config('railforums.author_table_name'))
            //                        ->limit(1)
            //                        ->whereRaw(
            //                            config('railforums.author_table_name') .
            //                            '.id = liker_1_id'
            //                        );
            //                },
            //                'liker_1_display_name'
            //            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('liker_id')
                        ->from(ConfigService::$tablePostLikes)
                        ->limit(1)
                        ->skip(1)
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.post_id = ' . ConfigService::$tablePosts . '.id'
                        )
                        ->orderBy('liked_on', 'desc');
                },
                'liker_2_id'
            )//            ->selectSub(
            //                function (Builder $builder) {
            //                    return $builder->select([
            //                            config('railforums.author_table_display_name_column_name')
            //                        ])
            //                        ->from(config('railforums.author_table_name'))
            //                        ->limit(1)
            //                        ->whereRaw(
            //                            config('railforums.author_table_name') .
            //                            '.id = liker_2_id'
            //                        );
            //                },
            //                'liker_2_display_name'
            //            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('liker_id')
                        ->from(ConfigService::$tablePostLikes)
                        ->limit(1)
                        ->skip(2)
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.post_id = ' . ConfigService::$tablePosts . '.id'
                        )
                        ->orderBy('liked_on', 'desc');
                },
                'liker_3_id'
            )//            ->selectSub(
            //                function (Builder $builder) {
            //                    return $builder->select([
            //                            config('railforums.author_table_display_name_column_name')
            //                        ])
            //                        ->from(config('railforums.author_table_name'))
            //                        ->limit(1)
            //                        ->whereRaw(
            //                            config('railforums.author_table_name') .
            //                            '.id = liker_3_id'
            //                        );
            //                },
            //                'liker_3_display_name'
            //            )
            ->whereNull(ConfigService::$tablePosts . '.deleted_at');
    }

    /**
     * Performs a chunked table read and creates search index records with the fetched data
     */
    public function createSearchIndexes()
    {
        $authorsTable = config('railforums.author_table_name');
        $authorsTableKey = config('railforums.author_table_id_column_name');
        $displayNameColumn = config('railforums.author_table_display_name_column_name');

        $query =
            $this->baseQuery()
                ->from(ConfigService::$tablePosts)
                ->select(ConfigService::$tablePosts . '.*')
                ->whereNull(ConfigService::$tablePosts . '.deleted_at')
                ->orderBy(ConfigService::$tablePosts . '.id');

        $instance = $this;

        $query->chunk(
            self::CHUNK_SIZE,
            function (Collection $postsData) use (
                $displayNameColumn,
                $instance
            ) {

                $chunk = [];
                $now =
                    Carbon::now()
                        ->toDateTimeString();

                $postEntities = new BaseCollection();

                foreach ($postsData as $postData) {
                    $postEntities[] = new Entity((array)$postData);
                }

                $postsData = $this->postUserDecorator->decorate($postEntities);

                foreach ($postsData as $postData) {

                    $searchIndex = [
                        'high_value' => $this->getFilteredPostContent($postData->content),
                        'medium_value' => null,
                        'low_value' => $postData->{$displayNameColumn},
                        'thread_id' => $postData->thread_id,
                        'post_id' => $postData->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $chunk[] = $searchIndex;
                }

                $instance->baseQuery()
                    ->from(ConfigService::$tableSearchIndexes)
                    ->insert($chunk);
            }
        );
    }

    /**
     * strips out blockquote tags with it's content, return the rest of the content, if not empty
     * if post content is composed just of blockquote tags, return the content without quoted post metadata
     */
    protected function getFilteredPostContent($content)
    {
        // filter out blockquote tags
        $crawler = new Crawler($content);

        $crawler->filter('blockquote')
            ->each(
                function (Crawler $crawler) {
                    foreach ($crawler as $node) {
                        $node->parentNode->removeChild($node);
                    }
                }
            );

        $result = $crawler->text();

        if ($result) {

            $result = trim($result, " \t\n\r\0\x0B" . chr(0xC2) . chr(0xA0));
        }

        //        if (!$result) {
        //            // if post contains only a blockquote tag
        //            // filter out the quoted post metadata
        //            $crawler = new Crawler($content);
        //
        //            $crawler->filter('span.post-id')->each(function (Crawler $crawler) {
        //                foreach ($crawler as $node) {
        //                    $node->parentNode->removeChild($node);
        //                }
        //            });
        //
        //            $crawler->filter('p.quote-heading')->each(function (Crawler $crawler) {
        //                foreach ($crawler as $node) {
        //                    $node->parentNode->removeChild($node);
        //                }
        //            });
        //
        //            $result = $crawler->text();
        //
        //            if ($result) {
        //
        //                $result = trim($result, " \t\n\r\0\x0B" . chr(0xC2).chr(0xA0));
        //            }
        //        }

        return (string)$result;
    }
}
