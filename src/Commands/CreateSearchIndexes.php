<?php

namespace Railroad\Railforums\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Railroad\Railforums\Repositories\SearchIndexRepository;
use Railroad\Railforums\Services\ConfigService;

class CreateSearchIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forums:rebuildSearchIndexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create search indexes';

    /**
     * Execute the console command.
     */
    public function handle(SearchIndexRepository $searchIndexRepository)
    {
        $timeStart = microtime(true);
        $this->info("Processing $this->name");

        $this->info('Starting ' . Carbon::now()->toDateTimeString());
        $this->info('RAM usage: ' . round(memory_get_usage(true) / 1048576, 2));

        foreach (config('railforums.brand_database_connection_names') as $brand => $dbConnectionName) {
            if ($brand != 'musora') {
                $railforumsConnectionName = config('railforums.brand_database_connection_names')[$brand];
                ConfigService::$databaseConnectionName = $railforumsConnectionName;
                config()->set('railforums.database_connection', $railforumsConnectionName);
                config()->set('railforums.database_connection_name', $railforumsConnectionName);
                config()->set('railforums.brand', $brand);

                $this->info('Starting forums search indexes for: ' . $brand);
                $searchIndexRepository->createSearchIndexes($brand);
                $this->info('Finished forums search indexes for: ' . $brand);
            }
        }

        $this->info('RAM usage: ' . round(memory_get_usage(true) / 1048576, 2));
        $this->info('End ' . Carbon::now()->toDateTimeString());


        $diff = microtime(true) - $timeStart;
        $sec = intval($diff);
        $this->info("Finished $this->name ($sec s)");
    }
}
