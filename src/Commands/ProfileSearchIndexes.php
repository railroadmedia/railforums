<?php

namespace Railroad\Railforums\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Railroad\Railforums\Repositories\SearchIndexRepository;
use Railroad\Railforums\Services\ConfigService;

class ProfileSearchIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forums:profileSearchIndexes 
                            {brand=drumeo : The brand to use}
                            {runs=5 : The number of times to run the query}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform a simple query on search indexes multiple times to profile the response time';

    /**
     * Execute the console command.
     */
    public function handle(SearchIndexRepository $searchIndexRepository)
    {
        $brand = $this->argument('brand');
        if (!array_key_exists($brand, config('railforums.brand_database_connection_names'))) {
            $this->error("$brand is not a valid brand");
            return;
        }

        $timeStart = microtime(true);
        $this->info("Processing $this->name");
        $this->info('Starting ' . Carbon::now()->toDateTimeString());

        $railforumsConnectionName = config('railforums.brand_database_connection_names')[$brand];
        ConfigService::$databaseConnectionName = $railforumsConnectionName;
        config()->set('railforums.database_connection', $railforumsConnectionName);
        config()->set('railforums.database_connection_name', $railforumsConnectionName);
        config()->set('railforums.brand', $brand);

        $runs = collect();
        $runsCount = $this->argument('runs');

        for ($run = 1; $run <= $runsCount; $run++ ) {
            $runTimeStart = microtime(true);
            $this->info('Starting search for run ' . $run);
            $searchIndexRepository->search(
                "say hello",
                1,
                10,
                "score"
            );

            $runTime = number_format(microtime(true) - $runTimeStart, 4);
            $runs->push($runTime);
            $this->info("Finished search for run $run in $runTime s");
        }

        $this->info('End ' . Carbon::now()->toDateTimeString());

        $diff = microtime(true) - $timeStart;
        $sec = intval($diff);
        $this->info("Finished $this->name ($sec s)");

        $runs = $runs->sort();
        $this->newLine();
        $this->info("Fastest run: {$runs->first()}");
        $this->info("Slowest run: {$runs->last()}");
        $this->info("Median run: {$runs->median()}");
    }
}
