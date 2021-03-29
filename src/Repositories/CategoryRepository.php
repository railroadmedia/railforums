<?php

namespace Railroad\Railforums\Repositories;

use Illuminate\Database\Query\Builder;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Resora\Queries\CachedQuery;
use Railroad\Resora\Repositories\RepositoryBase;

class CategoryRepository extends RepositoryBase
{
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
     * @param $amount
     * @param $page
     * @return mixed
     */
    public function getDecoratedCategories(
        $amount,
        $page
    ) {
        $query =
            $this->getDecoratedQuery()
                ->limit($amount)
                ->skip($amount * ($page - 1))
                ->orderByRaw('created_at desc, title asc')
                ->where(
                    ConfigService::$tableCategories . '.brand',
                    config('railforums.brand')
                );

        return $query->get();
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
                ->whereNull(ConfigService::$tableCategories . '.deleted_at');

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
            ->selectSub(
                function (Builder $builder) {

                    return $builder->selectRaw('COUNT(*)')
                        ->from(ConfigService::$tableThreads)
                        ->whereRaw(
                            ConfigService::$tableThreads . '.category_id = ' . ConfigService::$tableCategories . '.id'
                        )
                        ->whereNull(ConfigService::$tableThreads . '.deleted_at')
                        ->limit(1);
                },
                'thread_count'
            )
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
        return $this->getDecoratedQuery()
            ->whereIn(ConfigService::$tableCategories . '.id', $ids)
            ->get();
    }
}
