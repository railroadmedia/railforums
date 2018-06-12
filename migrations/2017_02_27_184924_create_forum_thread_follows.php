<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class CreateForumThreadFollows extends Migration
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
                ConfigService::$tableThreadFollows,
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('thread_id')->unsigned();
                    $table->integer('follower_id')->unsigned();
                    $table->dateTime('followed_on');

                    $table->timestamps();

                    $table->index(['thread_id']);
                    $table->index(['follower_id']);
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
        Schema::dropIfExists(ConfigService::$tableThreadFollows);
    }
}
