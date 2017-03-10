<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Events\PostReported;
use Railroad\Railforums\Services\PostReports\PostReportService;

class PostReportServiceTest extends TestCase
{
    /**
     * @var PostReportService
     */
    private $classBeingTested;

    public function setUp()
    {
        parent::setUp();

        $this->classBeingTested = app(PostReportService::class);
    }

    public function test_report_post()
    {
        $userId = rand();
        $postId = rand();

        $this->expectsEvents(PostReported::class);

        $postReport = $this->classBeingTested->report($postId, $userId);

        $this->assertDatabaseHas(
            'forum_post_reports',
            [
                'post_id' => $postId,
                'reporter_id' => $userId,
                'reported_on' => Carbon::now()->toDateTimeString()
            ]
        );

        $this->assertEquals($postReport->getPostId(), $postId);
        $this->assertEquals($postReport->getReporterId(), $userId);
        $this->assertEquals($postReport->getReportedOn(), Carbon::now()->toDateTimeString());
    }
}