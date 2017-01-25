<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumTablePosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_posts', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('thread_id')->unsigned();
            $table->integer('author_id')->unsigned();
            $table->integer('prompting_post_id')->nullable()->unsigned();
            $table->text('content');
            $table->integer('likes')->default(0);
            $table->dateTime('published_on')->nullable();
            $table->dateTime('edited_on')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->integer('version_master_id')->nullable();
            $table->timestamp('version_saved_at')->nullable();
        });
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
