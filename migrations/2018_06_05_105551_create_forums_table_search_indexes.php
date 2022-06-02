<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class CreateForumsTableSearchIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ((ConfigService::$databaseConnectionName != ConfigService::$connectionMaskPrefix . 'testbench') || (config('database.connections.' . config('database.default') . '.database') === ':memory:')) {

            Schema::connection(ConfigService::$databaseConnectionName)
                ->create(
                    ConfigService::$tableSearchIndexes,
                    function (Blueprint $table) {

                        $table->engine = 'InnoDB';
                        $table->increments('id');

                        $table->text('high_value')->nullable();
                        $table->text('medium_value')->nullable();
                        $table->text('low_value')->nullable();

                        $table->integer('thread_id')->index();
                        $table->integer('post_id')->index()->nullable();

                        $table->timestamps();
                    }
                );

            Schema::connection(ConfigService::$databaseConnectionName)
                ->getConnection()
                ->getPdo()
                ->exec(
                    'ALTER TABLE ' . ConfigService::$tableSearchIndexes . ' ' .
                    'ADD FULLTEXT high_full_text(high_value)'
                );

            Schema::connection(ConfigService::$databaseConnectionName)
                ->getConnection()
                ->getPdo()
                ->exec(
                    'ALTER TABLE ' . ConfigService::$tableSearchIndexes . ' ' .
                    'ADD FULLTEXT medium_full_text(medium_value)'
                );

            Schema::connection(ConfigService::$databaseConnectionName)
                ->getConnection()
                ->getPdo()
                ->exec(
                    'ALTER TABLE ' . ConfigService::$tableSearchIndexes . ' ' .
                    'ADD FULLTEXT low_full_text(low_value)'
                );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(ConfigService::$tableSearchIndexes);
    }
}
