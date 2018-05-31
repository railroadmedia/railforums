<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumTableThreadReads extends Migration
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
                'forum_thread_reads',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('thread_id')->unsigned();
                    $table->integer('reader_id')->unsigned();
                    $table->dateTime('read_on');

                    $table->timestamps();

                    $table->index(['thread_id']);
                    $table->index(['reader_id']);
                    $table->index(['read_on']);
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
        Schema::dropIfExists('forum_thread_reads');
    }
}
