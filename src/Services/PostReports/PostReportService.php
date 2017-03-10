<?php

namespace Railroad\Railforums\Services\PostReports;

use Carbon\Carbon;
use Railroad\Railforums\DataMappers\PostReportDataMapper;
use Railroad\Railforums\Entities\PostReport;
use Railroad\Railforums\Events\PostReported;

class PostReportService
{
    private $postReportDataMapper;

    public function __construct(PostReportDataMapper $postReportDataMapper)
    {
        $this->postReportDataMapper = $postReportDataMapper;
    }

    /**
     * @param $postId
     * @param $reporterId
     * @return PostReport
     */
    public function report($postId, $reporterId)
    {
        $postReport = new PostReport();
        $postReport->setPostId($postId);
        $postReport->setReporterId($reporterId);
        $postReport->setReportedOn(Carbon::now()->toDateTimeString());
        $postReport->persist();

        event(new PostReported($postReport->getId()));

        return $postReport;
    }
}