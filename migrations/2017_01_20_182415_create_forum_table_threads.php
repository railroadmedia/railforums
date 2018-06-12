<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class CreateForumTableThreads extends Migration
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
                ConfigService::$tableThreads,
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('category_id')->unsigned();
                    $table->integer('author_id')->unsigned();
                    $table->string('title');
                    $table->string('slug');
                    $table->boolean('pinned')->default(false);
                    $table->boolean('locked')->default(false);
                    $table->string('state');
                    $table->integer('post_count')->default(0);
                    $table->dateTime('published_on')->nullable();

                    $table->timestamps();
                    $table->softDeletes();
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
        Schema::dropIfExists(ConfigService::$tableThreads);
    }
}
