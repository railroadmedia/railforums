<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class CreateForumsTablePostReplies extends Migration
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
                ConfigService::$tablePostReplies,
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('child_post_id')->unsigned();
                    $table->integer('parent_post_id')->unsigned();

                    $table->index(['child_post_id']);
                    $table->index(['parent_post_id']);
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
        Schema::dropIfExists(ConfigService::$tablePostReplies);
    }
}
