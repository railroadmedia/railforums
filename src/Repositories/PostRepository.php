<?php

namespace Railroad\Railforums\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
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

    //    /**
    //     * @var UserProviderInterface
    //     */
    //    private $userProvider;

    //    /**
    //     * PostRepository constructor.
    //     *
    //     * @param UserProviderInterface $userProvider
    //     */
    //    public function __construct(UserProviderInterface $userProvider)
    //    {
    //        $this->userProvider = $userProvider;
    //    }

    public function getCreateEvent($entity)
    {
        return new PostCreated($entity->id, auth()->id());
    }

    public function getReadEvent($entity)
    {
        return null;
    }

    public function getUpdateEvent($entity)
    {
        $id = is_object($entity) ? $entity->id : $entity;

        return new PostUpdated($id, auth()->id());
    }

    public function getDestroyEvent($entity)
    {
        return null;
    }

    public function getDeleteEvent($id)
    {
        return new PostDeleted($id, auth()->id());
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
     * @return Collection
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
     * @return Collection
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
     * @return Collection
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
     * @return Builder
     */
    public function getDecoratedQuery()
    {
        $query = $this->query()
            ->select(ConfigService::$tablePosts . '.*')
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
            ->whereNull(ConfigService::$tablePosts . '.deleted_at');

        if (auth()->user()) {
            $query->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('COUNT(*) > 0')
                        ->from(ConfigService::$tablePostLikes)
                        ->limit(1)
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.post_id = ' . ConfigService::$tablePosts . '.id'
                        )
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.liker_id = ' . auth()->id() ?? 0
                        );
                },
                'is_liked_by_viewer'
            );
        }

        return $query;
    }

    /**
     * Performs a chunked table read and creates search index records with the fetched data
     */
    public function createSearchIndexes()
    {
        $query =
            $this->baseQuery()
                ->from(ConfigService::$tablePosts)
                ->join(ConfigService::$tableThreads, ConfigService::$tablePosts.'.thread_id', '=', ConfigService::$tableThreads.'.id')
                ->select(ConfigService::$tablePosts . '.*')
                ->whereNull(ConfigService::$tablePosts . '.deleted_at')
                ->whereNull(ConfigService::$tableThreads . '.deleted_at')
                ->orderBy(ConfigService::$tablePosts . '.id');

        $instance = $this;
        $chunk = [];

        $query->chunk(
            self::CHUNK_SIZE,
            function (Collection $postsData) use (
                $instance, $chunk
            ) {


                $now =
                    Carbon::now()
                        ->toDateTimeString();

                $postEntities = new BaseCollection();

                foreach ($postsData as $postData) {
                    $postEntities[] = new Entity((array)$postData);
                }

                $postsData = self::decorate($postEntities);

                foreach ($postsData as $index => $postData) {

                    $searchIndex = [
                        'high_value' => substr(utf8_encode($this->getFilteredPostContent($postData->content)), 0, 65535),
                        'medium_value' => null,
                        'low_value' => $postData->author_display_name,
                        'thread_id' => $postData->thread_id,
                        'post_id' => $postData->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $chunk[] = $searchIndex;
                }

                try {
                    $instance->baseQuery()
                        ->from(ConfigService::$tableSearchIndexes)
                        ->insert($chunk);
                } catch (\Exception $e) {
                   $this->info(print_r($e->getMessage(), true));
                }


            }
        );
    }

    /**
     * strips out blockquote tags with it's content, return the rest of the content, if not empty
     * if post content is composed just of blockquote tags, return the content without quoted post metadata
     */
    public function getFilteredPostContent($content)
    {
        // filter out blockquote tags
        $crawler = new Crawler($content);
        $result = $content;
        if ($crawler->filter('blockquote')->count() > 0) {
            $crawler->filter('blockquote')
                ->each(
                    function (Crawler $crawler) {
                        foreach ($crawler as $node) {
                            $node->parentNode->removeChild($node);
                        }
                    }
                );

            $result = $crawler->text();
        }

        if ($result) {

            $result = trim($result, " \t\n\r\0\x0B\xBFQui" . chr(0xC2) . chr(0xA0));
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

    /**
     * Returns the posts count of the specified users
     *
     * @param array $userIds
     *
     * @return array
     */
    public function getUsersPostsCount(array $userIds)
    {
        $postsCount =
            $this->connection()
                ->table(ConfigService::$tablePosts)
                ->selectRaw('author_id')
                ->selectRaw('COUNT(' . ConfigService::$tablePosts . '.id) as count')
                ->whereIn('author_id', $userIds)
                ->groupBy('author_id')
                ->get()
                ->toArray();

        return array_combine(array_column($postsCount, 'author_id'), array_column($postsCount, 'count'));
    }
}
