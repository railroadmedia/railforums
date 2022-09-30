<?php

namespace Railroad\Railforums\Decorators;

abstract class ModeDecoratorBase implements DecoratorInterface
{
    public static $decorationMode = DecoratorInterface::DECORATION_MODE_MAXIMUM;
}