<?php

namespace Railroad\Railforums\Events;

class PostReported
{
    private $postReportId;

    public function __construct($postReportId)
    {
        $this->postReportId = $postReportId;
    }

    /**
     * @return int
     */
    public function getPostReportId(): int
    {
        return $this->postReportId;
    }

    /**
     * @param int $postReportId
     */
    public function setPostReportId(int $postReportId)
    {
        $this->postReportId = $postReportId;
    }
}