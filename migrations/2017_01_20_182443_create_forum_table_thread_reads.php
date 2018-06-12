<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class CreateForumTableThreadReads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConfigService::$databaseConnectionName)
            ->create(
                ConfigService::$tableThreadReads,
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('thread_id')->unsigned();
                    $table->integer('reader_id')->unsigned();
                    $table->dateTime('read_on');

                    $table->timestamps();

                    $table->index(['thread_id']);
                    $table->index(['reader_id']);
                    $table->index(['read_on']);
                }
            );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(ConfigService::$tableThreadReads);
    }
}
