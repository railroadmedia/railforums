<?php

namespace Railroad\Railforums\DataMappers;

use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\UserCloak;
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
     * @var UserCloak
     */
    protected $currentUserCloak;

    /**
     * @var UserCloakDataMapper
     */
    protected $userCloakDataMapper;

    public function __construct()
    {
        parent::__construct();

        $this->userCloakDataMapper = app(UserCloakDataMapper::class);
        $this->currentUserCloak = $this->userCloakDataMapper->getCurrent();
    }
}