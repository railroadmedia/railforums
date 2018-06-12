<?php

namespace Railroad\Railforums\Decorators;

use Illuminate\Database\DatabaseManager;
use Railroad\Permissions\Services\ConfigService;
use Railroad\Resora\Collections\BaseCollection;
use Railroad\Resora\Decorators\DecoratorInterface;

class ThreadDecorator implements DecoratorInterface
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
    public function decorate($threads)
    {
        // echo "\n\n ###ThreadDecorator::decorate threadsIds: " . var_export($threads->pluck('id'), true) . "\n\n";

        return $threads;
    }
}
