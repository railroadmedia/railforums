<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class CreateForumTablePostLikes extends Migration
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
                ConfigService::$tablePostLikes,
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('post_id')->unsigned();
                    $table->integer('liker_id')->unsigned();
                    $table->dateTime('liked_on');

                    $table->timestamps();

                    $table->index(['post_id']);
                    $table->index(['liker_id']);
                    $table->index(['liked_on']);
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
        Schema::dropIfExists(ConfigService::$tablePostLikes);
    }
}
