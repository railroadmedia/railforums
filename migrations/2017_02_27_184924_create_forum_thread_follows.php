<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumThreadFollows extends Migration
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
                'forum_thread_follows',
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
        Schema::dropIfExists('forum_thread_follows');
    }
}
