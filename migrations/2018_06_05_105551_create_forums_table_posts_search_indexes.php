<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumsTablePostsSearchIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('railforums.database_connection_name') != 'testbench') {
            Schema::connection(config('railforums.database_connection_name'))
                ->create(
                    'forum_posts_search_indexes',
                    function (Blueprint $table) {

                        $table->engine = 'InnoDB';
                        $table->increments('id');

                        $table->text('high_value');
                        $table->text('medium_value');
                        $table->text('low_value');

                        $table->timestamps();
                    }
                );

            Schema::connection(config('railforums.database_connection_name'))
                ->getConnection()
                ->getPdo()
                ->exec(
                    'ALTER TABLE forum_posts_search_indexes ' .
                    'ADD FULLTEXT high_full_text(high_value)'
                );

            Schema::connection(config('railforums.database_connection_name'))
                ->getConnection()
                ->getPdo()
                ->exec(
                    'ALTER TABLE forum_posts_search_indexes ' .
                    'ADD FULLTEXT medium_full_text(medium_value)'
                );
            Schema::connection(config('railforums.database_connection_name'))
                ->getConnection()
                ->getPdo()
                ->exec(
                    'ALTER TABLE forum_posts_search_indexes ' .
                    'ADD FULLTEXT low_full_text(low_value)'
                );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forum_posts_search_indexes');
    }
}
