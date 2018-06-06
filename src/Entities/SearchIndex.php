<?php

namespace Railroad\Railforums\Entities;

use Carbon\Carbon;
use Faker\Generator;
use Railroad\Railforums\DataMappers\SearchIndexDataMapper;
use Railroad\Railmap\Entity\EntityBase;
use Railroad\Railmap\Entity\Properties\Timestamps;

class SearchIndex extends EntityBase
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
     * @var int
     */
    private $threadId;

    /**
     * @var int
     */
    private $postId;

    /**
     * SearchIndex constructor.
     */
    public function __construct()
    {
        $this->setOwningDataMapper(app(SearchIndexDataMapper::class));
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
     * @return SearchIndex
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
     * @return SearchIndex
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
     * @return SearchIndex
     */
    public function setLowValue($lowValue)
    {
        $this->lowValue = $lowValue;

        return $this;
    }

    /**
     * @return int
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * @param int $threadId
     *
     * @return SearchIndex
     */
    public function setThreadId($threadId)
    {
        $this->threadId = $threadId;

        return $this;
    }

    /**
     * @return int
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param int $postId
     *
     * @return SearchIndex
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;

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
        $this->setThreadId($faker->numberBetween());
        $this->setPostId($faker->numberBetween());
        $this->setCreatedAt(Carbon::instance($faker->dateTime)->toDateTimeString());
    }
}