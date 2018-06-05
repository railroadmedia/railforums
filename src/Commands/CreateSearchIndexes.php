<?php

namespace Railroad\Railforums\Commands;

use Illuminate\Console\Command;
use Railroad\Railforums\DataMappers\PostsSearchIndexDataMapper;

class CreateSearchIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:createSearchIndexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create search indexes';

    /**
     * @var PostsSearchIndexDataMapper
     */
    protected $postsSearchIndexDataMapper;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        PostsSearchIndexDataMapper $postsSearchIndexDataMapper
    ) {
        parent::__construct();

        $this->postsSearchIndexDataMapper = $postsSearchIndexDataMapper;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->postsSearchIndexDataMapper->createSearchIndexes();
    }
}
