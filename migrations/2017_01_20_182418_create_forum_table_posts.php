<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumTablePosts extends Migration
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
                'forum_posts',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('thread_id')->unsigned();
                    $table->integer('author_id')->unsigned();
                    $table->integer('prompting_post_id')->nullable()->unsigned();
                    $table->text('content');
                    $table->string('state');
                    $table->dateTime('published_on')->nullable();
                    $table->dateTime('edited_on')->nullable();

                    $table->timestamps();
                    $table->softDeletes();

                    $table->integer('version_master_id')->nullable();
                    $table->timestamp('version_saved_at')->nullable();

                    $table->index(['thread_id']);
                    $table->index(['author_id']);
                    $table->index(['prompting_post_id']);
                    $table->index(['state']);
                    $table->index(['published_on']);
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
        Schema::dropIfExists('forum_posts');
    }
}
