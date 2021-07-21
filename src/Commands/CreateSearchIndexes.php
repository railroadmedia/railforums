<?php

namespace Railroad\Railforums\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Railroad\Railforums\Repositories\SearchIndexRepository;

class CreateSearchIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:createForumSearchIndexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create search indexes';

    /**
     * @var SearchIndexRepository
     */
    protected $searchIndexRepository;

    /**
     * Create a new command instance.
     *
     * @param SearchIndexRepository $searchIndexRepository
     *
     * @return void
     */
    public function __construct(
        SearchIndexRepository $searchIndexRepository
    ) {
        parent::__construct();

        $this->searchIndexRepository = $searchIndexRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting '.Carbon::now()->toDateTimeString());
        $this->info('RAM usage: ' . round(memory_get_usage(true) / 1048576, 2));
        $this->searchIndexRepository->createSearchIndexes();

        $this->info('RAM usage: ' . round(memory_get_usage(true) / 1048576, 2));
        $this->info('End '.Carbon::now()->toDateTimeString());
    }
}
