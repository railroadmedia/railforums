<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumTableThreadReads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_thread_reads', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('thread_id')->unsigned();
            $table->integer('reader_id')->unsigned();
            $table->dateTime('read_on');

            $table->timestamps();

            $table->index(['thread_id']);
            $table->index(['reader_id']);
            $table->index(['read_on']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forum_thread_reads');
    }
}
