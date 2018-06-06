<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumTableThreads extends Migration
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
                'forum_threads',
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

                    $table->integer('version_master_id')->nullable();
                    $table->timestamp('version_saved_at')->nullable();
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
        Schema::dropIfExists('forum_threads');
    }
}
