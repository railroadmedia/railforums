<?php

namespace Railroad\Railforums\Decorators;


interface DecoratorInterface
{
    const DECORATION_MODE_MAXIMUM = 'maximum';
    const DECORATION_MODE_PARTIAL = 'partial';
    const DECORATION_MODE_MINIMUM = 'minimum';

    /**
     * @param $data
     * @return mixed
     */
    public function decorate($data);
}