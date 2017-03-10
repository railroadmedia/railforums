<?php

namespace Railroad\Railforums\Entities;

use Carbon\Carbon;
use Faker\Generator;
use Railroad\Railforums\DataMappers\PostReportDataMapper;
use Railroad\Railmap\Entity\EntityBase;
use Railroad\Railmap\Entity\Properties\Timestamps;

/**
 * Class PostReport
 *
 * @package Railroad\Railforums\Entities
 * @method PostReportDataMapper getOwningDataMapper()
 * @method UserCloak|null getReporter()
 * @method setReporter(UserCloak | null $reporter)
 * @method Post|null getPost()
 * @method setPort(Post | null $post)
 */
class PostReport extends EntityBase
{
    use Timestamps;

    /**
     * @var int
     */
    protected $postId;

    /**
     * @var int
     */
    protected $reporterId;

    /**
     * @var string
     */
    protected $reportedOn;

    public function __construct()
    {
        $this->setOwningDataMapper(app(PostReportDataMapper::class));
    }

    /**
     * @return int
     */
    public function getPostId(): int
    {
        return $this->postId;
    }

    /**
     * @param int $postId
     */
    public function setPostId(int $postId)
    {
        $this->postId = $postId;
    }

    /**
     * @return int
     */
    public function getReporterId(): int
    {
        return $this->reporterId;
    }

    /**
     * @param int $reporterId
     */
    public function setReporterId(int $reporterId)
    {
        $this->reporterId = $reporterId;
    }

    /**
     * @return string
     */
    public function getReportedOn(): string
    {
        return $this->reportedOn;
    }

    /**
     * @param string $reportedOn
     */
    public function setReportedOn(string $reportedOn)
    {
        $this->reportedOn = $reportedOn;
    }

    public function randomize()
    {
        /** @var Generator $faker */
        $faker = app(Generator::class);

        $this->setPostId($faker->randomNumber());
        $this->setReporterId($faker->randomNumber());
        $this->setReportedOn(Carbon::instance($faker->dateTime)->toDateTimeString());
    }
}