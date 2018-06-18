<?php

namespace Railroad\Railforums\Commands;

use Illuminate\Console\Command;
use Railroad\Railforums\Repositories\SearchIndexRepository;

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
        $this->searchIndexRepository->createSearchIndexes();
    }
}
