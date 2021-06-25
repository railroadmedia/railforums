<?php

namespace Railroad\Railforums\Decorators;

use Railroad\Resora\Decorators\DecoratorInterface;

class StripTagDecorator implements DecoratorInterface
{
    public function decorate($entities)
    {
        foreach ($entities as $entityIndex => $entity) {
                $entities[$entityIndex]['content'] = strip_tags(html_entity_decode($entity['content']));
        }

        return $entities;
    }
}