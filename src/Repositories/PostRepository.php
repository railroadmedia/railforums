<?php

namespace Railroad\Railforums\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Railroad\Railforums\Contracts\UserProviderInterface;
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
         * @var UserProviderInterface
         */
        private $userProvider;

        /**
         * PostRepository constructor.
         *
         * @param UserProviderInterface $userProvider
         */
        public function __construct(UserProviderInterface $userProvider)
        {
            $this->userProvider = $userProvider;
        }

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
        return $this->baseQuery()
            ->from(ConfigService::$tablePosts)
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
     * Get post data for search indexes
     */
    public function createSearchIndexes()
    {
        $query =
            $this->baseQuery()
                ->from(ConfigService::$tablePosts)
                ->join(ConfigService::$tableThreads, ConfigService::$tablePosts . '.thread_id', '=', ConfigService::$tableThreads . '.id')
                ->select(ConfigService::$tablePosts . '.*')
                ->whereNull(ConfigService::$tablePosts . '.deleted_at')
                ->whereNull(ConfigService::$tableThreads . '.deleted_at')
                ->whereIn(
                    ConfigService::$tablePosts . '.state',
                    self::ACCESSIBLE_STATES
                )
                ->orderBy(ConfigService::$tablePosts . '.id');
        $posts = [];
        $now =
            Carbon::now()
                ->toDateTimeString();

        $query->chunk(
            self::CHUNK_SIZE,
            function (Collection $postsData) use (
                &$posts, $now
            ) {

                $userIds =     $postsData->pluck('author_id')
                        ->toArray();
                $userIds = array_unique($userIds);
                $users = $this->userProvider->getUsersByIds($userIds);

                foreach ($postsData as $postData) {
                    $author = $users[$postData->author_id] ?? null;
                    $posts[] = [
                        'high_value' => substr(utf8_encode($this->getFilteredPostContent($postData->content)), 0, 65535),
                        'medium_value' => null,
                        'low_value' => $author ? $author->getDisplayName() : '',
                        'thread_id' => $postData->thread_id,
                        'post_id' => $postData->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                        'published_on' => $postData->published_on
                    ];
                }
            });

        return $posts;
    }

    /**
     * strips out blockquote tags with it's content, return the rest of the content, if not empty
     * if post content is composed just of blockquote tags, return the content without quoted post metadata
     */
    public function getFilteredPostContent($content)
    {
        return  preg_replace(
            "~<blockquote(.*?)>(.*)</blockquote>~si",
            "",
            $content
        );
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
