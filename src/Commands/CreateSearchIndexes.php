<?php

namespace Railroad\Railforums\Commands;

use Illuminate\Console\Command;
use Railroad\Railforums\DataMappers\SearchIndexDataMapper;

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
     * @var SearchIndexDataMapper
     */
    protected $searchIndexDataMapper;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        SearchIndexDataMapper $searchIndexDataMapper
    ) {
        parent::__construct();

        $this->searchIndexDataMapper = $searchIndexDataMapper;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->searchIndexDataMapper->createSearchIndexes();
    }
}
