<?php

namespace Railroad\Railforums\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Railroad\Railforums\Repositories\CategoryRepository;
use Railroad\Railforums\Repositories\SearchIndexRepository;
use Railroad\Railforums\Repositories\ThreadRepository;
use Railroad\Railforums\Services\ConfigService;

class PopulateLastPostOnForums extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:PopulateLastPostAndPostCountOnForums';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate Last Post Id and  Post Count on Forums and threads';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseManager $databaseManager, CategoryRepository $categoryRepository, ThreadRepository $threadRepository)
    {
        $this->info('Starting ' . Carbon::now()->toDateTimeString());
        $this->info('RAM usage: ' . round(memory_get_usage(true) / 1048576, 2));

        foreach (config('railforums.brand_database_connection_names') as $brand => $dbConnectionName) {
            $railforumsConnectionName = config('railforums.brand_database_connection_names')[$brand];
            ConfigService::$databaseConnectionName = $railforumsConnectionName;
            config()->set('railforums.database_connection', $railforumsConnectionName);
            config()->set('railforums.database_connection_name', $railforumsConnectionName);
            config()->set('railforums.brand', $brand);

            $discussions =  $databaseManager->connection($railforumsConnectionName)->table(ConfigService::$tableCategories)->get();
            foreach ($discussions as $discussion) {
                $forums =
                    $databaseManager->connection($railforumsConnectionName)
                        ->table(ConfigService::$tablePosts.' as p')
                        ->join(ConfigService::$tableThreads.' as t', 't.id', '=', 'p.thread_id')
                        ->selectRaw(
                            'COUNT(*) as post_count'
                        )
                        ->whereNull('p.deleted_at')
                        ->whereNull('t.deleted_at')
                        ->where('t.category_id', $discussion->id)
                        ->first();

                $post_count = $forums->post_count ?? 0;
                $this->info('categ '.$discussion->id. '    count '.$post_count);

                $latestPost = $databaseManager->connection($railforumsConnectionName)
                    ->table(ConfigService::$tablePosts . ' as p')
                    ->join(ConfigService::$tableThreads . ' as t', 't.id', '=', 'p.thread_id')
                    ->select(
                        'p.id as post_id',
                    )
                    ->whereNull('p.deleted_at')
                    ->whereNull('t.deleted_at')
                    ->where('t.category_id', $discussion->id)
                    ->orderBy('p.published_on', 'desc')
                    ->limit(1)
                    ->first();

                $categoryRepository->update($discussion->id, ['post_count' => $post_count, 'last_post_id' => $latestPost->post_id ?? null]);
            }

            $threads =  $databaseManager->connection($railforumsConnectionName)->table(ConfigService::$tableThreads)->get();
            foreach ($threads as $thread) {
                $forums =
                    $databaseManager->connection($railforumsConnectionName)
                        ->table(ConfigService::$tablePosts.' as p')
                        ->selectRaw(
                            'COUNT(*) as post_count'
                        )
                        ->whereNull('p.deleted_at')
                        ->where('p.thread_id', $thread->id)
                        ->first();

                $post_count = $forums->post_count ?? 0;
                $this->info('thread '.$thread->id. '    count '.$post_count);

                $latestPost = $databaseManager->connection($railforumsConnectionName)
                    ->table(ConfigService::$tablePosts . ' as p')
                    ->select(
                        'p.id as post_id',
                    )
                    ->whereNull('p.deleted_at')
                    ->where('p.thread_id', $thread->id)
                    ->orderBy('p.published_on', 'desc')
                    ->limit(1)
                    ->first();

                $threadRepository->update($thread->id, ['post_count' => $post_count, 'last_post_id' => $latestPost->post_id ?? null]);
            }
        }

        $this->info('RAM usage: ' . round(memory_get_usage(true) / 1048576, 2));
        $this->info('End ' . Carbon::now()->toDateTimeString());
    }
}
