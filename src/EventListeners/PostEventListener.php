<?php

namespace Railroad\Railforums\EventListeners;

use Carbon\Carbon;
use Railroad\Railforums\DataMappers\PostDataMapper;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\PostLike;
use Railroad\Railforums\Entities\ThreadFollow;
use Railroad\Railforums\Entities\ThreadRead;
use Railroad\Railmap\Events\EntityDestroyed;
use Railroad\Railmap\Events\EntitySaved;

class PostEventListener
{
    public function __construct()
    {

    }

    public function onCreated($id)
    {

    }

    public function onUpdated($id)
    {

    }

    public function onDeleted($id)
    {

    }
}