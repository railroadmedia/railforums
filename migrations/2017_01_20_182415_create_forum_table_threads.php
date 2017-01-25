<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumTableThreads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_threads', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->integer('author_id')->unsigned();
            $table->string('title');
            $table->string('slug');
            $table->boolean('pinned')->nullable()->default(0);
            $table->boolean('locked')->nullable()->default(0);
            $table->dateTime('last_posted_on');
            $table->dateTime('published_on');

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
        Schema::dropIfExists('forum_threads');
    }
}
