<?php

namespace Railroad\Railforums\Transformers;

use League\Fractal\TransformerAbstract;
use Railroad\Resora\Entities\Entity;

class ThreadTransformer extends TransformerAbstract
{
    public function transform(Entity $thread)
    {
        return $thread->getArrayCopy();
    }
}
