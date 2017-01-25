<?php

use Railroad\Railforums\DataMappers\CategoryDataMapper;
use Railroad\Railforums\Entities\Category;
use Tests\TestCase;

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
        $entity->persist();

        $extracted = $this->classBeingTested->extract($entity);

        $this->assertEquals(
            array_values($this->classBeingTested->mapFrom()),
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