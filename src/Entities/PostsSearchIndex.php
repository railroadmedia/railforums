<?php

namespace Railroad\Railforums\Entities;

use Carbon\Carbon;
use Faker\Generator;
use Railroad\Railforums\DataMappers\PostsSearchIndexDataMapper;
use Railroad\Railmap\Entity\EntityBase;
use Railroad\Railmap\Entity\Properties\Timestamps;

class PostsSearchIndex extends EntityBase
{
    use Timestamps;

    /**
     * @var string
     */
    private $highValue;

    /**
     * @var string
     */
    private $mediumValue;

    /**
     * @var string
     */
    private $lowValue;

    /**
     * PostsSearchIndex constructor.
     */
    public function __construct()
    {
        $this->setOwningDataMapper(app(PostsSearchIndexDataMapper::class));
    }

    /**
     * @return string
     */
    public function getHighValue()
    {
        return $this->highValue;
    }

    /**
     * @param string $highValue
     *
     * @return PostsSearchIndex
     */
    public function setHighValue($highValue)
    {
        $this->highValue = $highValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getMediumValue()
    {
        return $this->mediumValue;
    }

    /**
     * @param string $mediumValue
     *
     * @return PostsSearchIndex
     */
    public function setMediumValue($mediumValue)
    {
        $this->mediumValue = $mediumValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getLowValue()
    {
        return $this->lowValue;
    }

    /**
     * @param string $lowValue
     *
     * @return PostsSearchIndex
     */
    public function setLowValue($lowValue)
    {
        $this->lowValue = $lowValue;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function randomize()
    {
    	/** @var Generator $faker */
        $faker = app(Generator::class);

        $this->setHighValue($faker->paragraph());
        $this->setMediumValue($faker->paragraph());
        $this->setLowValue($faker->paragraph());
        $this->setCreatedAt(Carbon::instance($faker->dateTime)->toDateTimeString());
    }
}