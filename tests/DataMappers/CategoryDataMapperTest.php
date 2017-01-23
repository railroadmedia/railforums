<?php

use Orchestra\Testbench\TestCase;
use Railroad\Railforums\DataMappers\CategoryDataMapper;
use Railroad\Railforums\Entities\Category;

class CategoryDataMapperTest extends TestCase
{
    /**
     * @var CategoryDataMapper
     */
    private $classBeingTested;

    public function setUp()
    {
        parent::setUp();

        $this->classBeingTested = app(CategoryDataMapper::class);
    }

    public function test_map()
    {
        $entity = new Category();
        $entity->randomize();

        $extracted = $this->classBeingTested->extract($entity);

        $this->assertEquals(
            array_values($this->classBeingTested->map()),
            array_keys($extracted)
        );

        $entityClone = clone $entity;

        $this->classBeingTested->fill($entityClone, $extracted);

        $this->assertEquals(
            $entity,
            $entityClone
        );
    }
}