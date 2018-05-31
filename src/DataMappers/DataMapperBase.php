<?php

namespace Railroad\Railforums\DataMappers;

use Railroad\Railforums\Entities\Post;
use Railroad\Railmap\DataMapper\DatabaseDataMapperBase;

/**
 * Class PostDataMapper
 *
 * @package Railroad\Railforums\DataMappers
 * @method Post|Post[] getWithQuery(callable $queryCallback, $forceArrayReturn = false)
 * @method Post|Post[] get($idOrIds)
 */
abstract class DataMapperBase extends DatabaseDataMapperBase
{
    /**
     * @var UserCloakDataMapper
     */
    protected $userCloakDataMapper;

    public function __construct()
    {
        parent::__construct();

        $this->userCloakDataMapper = app(UserCloakDataMapper::class);
    }

    public function baseQuery()
    {
        $query = $this->databaseManager->connection(config('railforums.database_connection_name'))->query();

        $query->from($this->table);

        // If the entity has soft deletes, we only want to pull the non-soft deleted rows
        if (isset(array_flip($this->mapTo())['deleted_at'])) {
            $query->whereNull($this->table . '.deleted_at');
        }

        // If the entity has version-ing, we only want to pull version masters
        if (isset(array_flip($this->mapTo())['version_master_id'])) {
            $query->whereNull($this->table . '.version_master_id');
        }

        return $query;
    }
}