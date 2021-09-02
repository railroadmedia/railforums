<?php

namespace Railroad\Railforums\Decorators;

use Railroad\Resora\Decorators\DecoratorInterface;

class EmojiesDecorator implements DecoratorInterface
{
    public function decorate($entities)
    {
        foreach ($entities as $entityIndex => $entity) {
            $entities[$entityIndex]['content'] = preg_replace(
                '~\<img (.*?) alt="(.*?)" data-emoticon="true" />~s',
                '$2',
                $entity['content']
            );
        }

        return $entities;
    }
}