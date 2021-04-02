<?php

namespace Railroad\Railforums\Decorators;

use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\DB;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Resora\Collections\BaseCollection;
use Railroad\Resora\Decorators\DecoratorInterface;

class DiscussionDecorator implements DecoratorInterface
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * @param BaseCollection $threads
     * @return BaseCollection
     */
    public function decorate($discussions)
    {
        foreach ($discussions as $discussion) {
            $lastPost =
                $this->databaseManager->connection(config('railforums.database_connection'))
                    ->table(ConfigService::$tableCategories)
                    ->leftJoin(
                        ConfigService::$tableThreads,
                        ConfigService::$tableCategories . '.id',
                        '=',
                        ConfigService::$tableThreads . '.category_id'
                    )
                    ->leftJoin(
                        ConfigService::$tablePosts,
                        function ($leftJoin) {
                            $leftJoin->on(
                                ConfigService::$tableThreads . '.id',
                                '=',
                                ConfigService::$tablePosts . '.thread_id'
                            )
                                ->where(
                                    ConfigService::$tablePosts . '.created_at',
                                    '=',
                                    DB::raw(
                                        "(select max(`created_at`) from " . ConfigService::$tablePosts . ")"
                                    )
                                );
                        }
                    )
                    ->where(ConfigService::$tableCategories . '.id', $discussion['id'])
                    ->get();

        }
        return $discussions;
    }
}
