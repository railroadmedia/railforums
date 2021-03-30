<?php

namespace Railroad\Railforums\Contracts;

interface UserProviderInterface
{
    /**
     * @return mixed
     */
    public function getCurrentUser();
}
