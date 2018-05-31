<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumsTablePostReplies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(config('railforums.database_connection_name'))
            ->create(
                'forum_post_replies',
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
        Schema::dropIfExists('forum_post_replies');
    }
}
