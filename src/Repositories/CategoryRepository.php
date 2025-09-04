<?php

namespace Railroad\Railforums\Repositories;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Railroad\Railforums\Contracts\UserProviderInterface;
use Railroad\Railforums\Decorators\DiscussionDecorator;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Resora\Queries\BaseQuery;
use Railroad\Resora\Queries\CachedQuery;

class CategoryRepository extends EventDispatchingRepository
{
    /**
     * @var DiscussionDecorator
     */
    private $discussionDecorator;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * CategoryRepository constructor.
     *
     * @param DiscussionDecorator $discussionDecorator
     */
    public function __construct(
        DiscussionDecorator $discussionDecorator,
        UserProviderInterface $userProvider
    ) {
        $this->discussionDecorator = $discussionDecorator;
        $this->userProvider = $userProvider;
    }

    // Override the read method from EventDispatchingRepository
    public function read($attributes)
    {
        // First get the category using the parent's read method
        $entity = parent::read($attributes);

        // If category doesn't exist, return null
        if (!$entity) {
            return null;
        }

        // Check if user is admin - admins can access all categories
        $isAdmin = $this->userProvider->isAdmin();
        if ($isAdmin) {
            return $entity;
        }

        // Get user's permission IDs
        $userPermissionIds = $this->userProvider->getUserPermissionIds();

        // Check if category has no permissions required
        $noPermissionsRequired = !$this->connection()->table('forum_categories_permissions')
            ->where('forum_category_id', $entity->id)
            ->exists();

        if ($noPermissionsRequired) {
            return $entity; // Return category if no permissions are required
        }

        // Check if user has at least one required permission
        $hasPermission = $this->connection()->table('forum_categories_permissions')
            ->where('forum_category_id', $entity->id)
            ->whereIn('permission_id', $userPermissionIds)
            ->exists();

        // Return the entity only if user has permission, otherwise null
        return $hasPermission ? $entity : null;
    }

    /**
     * @return CachedQuery|$this
     */
    protected function newQuery()
    {
        return (new CachedQuery($this->connection()))->from(ConfigService::$tableCategories);
    }

    protected function baseQuery()
    {
        return new BaseQuery($this->connection());
    }

    protected function connection()
    {
        return app('db')->connection(ConfigService::$databaseConnectionName);
    }

    /**
     * @return mixed
     */
    public function getDecoratedCategories($amount = null, $page = null)
    {
        $query =
            $this->getDecoratedQuery()
                ->orderByRaw('weight asc')
                ->where(
                    ConfigService::$tableCategories . '.brand',
                    config('railforums.brand')
                )
                ->whereNotIn(
                    ConfigService::$tableCategories . '.id',
                    config('railforums.excludedOldForumsIds.' . config('railforums.brand'), [])
                );

        if ($amount) {
            $query =
                $query->limit($amount)
                    ->skip($amount * ($page - 1));
        }

        $discussions = $query->get();

        return $this->discussionDecorator->decorate($discussions);
    }

    /**
     * @return mixed|null
     */
    public function getCategoriesCount()
    {
        $query =
            $this->query()
                ->selectRaw('COUNT(' . ConfigService::$tableCategories . '.id) as count')
                ->where(
                    ConfigService::$tableCategories . '.brand',
                    config('railforums.brand')
                )
                ->whereNull(ConfigService::$tableCategories . '.deleted_at')
                ->whereNotIn(ConfigService::$tableCategories . '.id', config('railforums.excludedOldForumsIds', []));

        return $query->value('count');
    }

    /**
     * Returns a decorated query to retrieve categories and associated data
     *
     * @return Builder
     */
    public function getDecoratedQuery()
    {
        $query = $this->query()
            ->select(ConfigService::$tableCategories . '.*')
            ->whereNull(ConfigService::$tableCategories . '.deleted_at');

        $isAdmin = $this->userProvider->isAdmin();
        if ($isAdmin) {
            return $query;
        }
        $userIds = $this->userProvider->getUserPermissionIds();
        $categoriesTable = ConfigService::$tableCategories;
        return $query->where(function ($query) use ($userIds, $categoriesTable) {
            // Include categories that don't require any permissions
            $query->whereNotExists(function ($subquery) use ($categoriesTable) {
                $subquery->select(DB::raw(1))
                    ->from('forum_categories_permissions')
                    ->whereColumn('forum_categories_permissions.forum_category_id', "$categoriesTable.id");
            })
                // OR include categories where the user has at least one of the required permissions
                ->orWhereExists(function ($subquery) use ($userIds, $categoriesTable) {
                    $subquery->select(DB::raw(1))
                        ->from('forum_categories_permissions')
                        ->whereColumn('forum_categories_permissions.forum_category_id', "$categoriesTable.id")
                        ->whereIn('forum_categories_permissions.permission_id', $userIds);
                });
        });
    }

    /**
     * Returns the categories and associated data
     *
     * @param array $ids
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDecoratedCategoriesByIds($ids)
    {
        return $this->discussionDecorator->decorate(
            $this->getDecoratedQuery()
                ->whereIn(ConfigService::$tableCategories . '.id', $ids)
                ->get()
        );
    }

    /**
     * @param $string
     *
     * @return string
     */
    public static function sanitizeForSlug($string)
    {
        return strtolower(
            preg_replace(
                '/(\-)+/',
                '-',
                str_replace(' ', '-', preg_replace('/[^ \w]+/', '', str_replace('&', 'and', trim($string))))
            )
        );
    }

    public function getCreateEvent($entity)
    {
        return null;
    }

    public function getReadEvent($entity)
    {
        return null;
    }

    public function getUpdateEvent($entity)
    {
        return null;
    }

    public function getDestroyEvent($entity)
    {
        return null;
    }

    public function getDeleteEvent($entity)
    {
        return null;
    }

    public function setLastPostId($categoryId, $postId)
    {
        $query = $this->query();

        return $query->update($categoryId, ['last_post_id' => $postId]);
    }

    /**
     * @param $threadId
     * @return mixed
     */
    public function calculateLastPostId($discussionId)
    {
        return $this->baseQuery()
            ->from(ConfigService::$tablePosts . ' as p')
            ->join(ConfigService::$tableThreads . ' as t', 't.id', '=', 'p.thread_id')
            ->select(
                'p.id as post_id',
            )
            ->whereNull('p.deleted_at')
            ->whereNull('t.deleted_at')
            ->where('t.category_id', $discussionId)
            ->orderBy('p.published_on', 'desc')
            ->limit(1)
            ->first();
    }
}
