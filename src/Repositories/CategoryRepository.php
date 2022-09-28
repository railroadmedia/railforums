<?php

namespace Railroad\Railforums\Repositories;

use Illuminate\Database\Query\Builder;
use Railroad\Railforums\Decorators\DiscussionDecorator;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Resora\Queries\CachedQuery;

class CategoryRepository extends EventDispatchingRepository
{
    /**
     * @var DiscussionDecorator
     */
    private $discussionDecorator;

    /**
     * CategoryRepository constructor.
     *
     * @param DiscussionDecorator $discussionDecorator
     */
    public function __construct(DiscussionDecorator $discussionDecorator)
    {
        $this->discussionDecorator = $discussionDecorator;
    }

    /**
     * @return CachedQuery|$this
     */
    protected function newQuery()
    {
        return (new CachedQuery($this->connection()))->from(ConfigService::$tableCategories);
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
                ->whereNotIn(ConfigService::$tableCategories . '.id', config('railforums.excludedOldForumsIds.'.config('railforums.brand'), []));

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
        return $this->query()
            ->select(ConfigService::$tableCategories . '.*')
            ->whereNull(ConfigService::$tableCategories . '.deleted_at');
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
        return $this->discussionDecorator->decorate($this->getDecoratedQuery()
            ->whereIn(ConfigService::$tableCategories . '.id', $ids)
            ->get());
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
}
