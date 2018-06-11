<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumsTableSearchIndexes extends Migration
{
    const TABLE_NAME = 'forum_search_indexes';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('railforums.database_connection_name') != config('railforums.connection_mask_prefix') . 'testbench') {

            Schema::connection(config('railforums.database_connection_name'))
                ->create(
                    self::TABLE_NAME,
                    function (Blueprint $table) {

                        $table->engine = 'InnoDB';
                        $table->increments('id');

                        $table->text('high_value')->nullable();
                        $table->text('medium_value')->nullable();
                        $table->text('low_value')->nullable();

                        $table->integer('thread_id')->index();
                        $table->integer('post_id')->index()->nullable();

                        $table->timestamps();
                    }
                );

            Schema::connection(config('railforums.database_connection_name'))
                ->getConnection()
                ->getPdo()
                ->exec(
                    'ALTER TABLE ' . self::TABLE_NAME . ' ' .
                    'ADD FULLTEXT high_full_text(high_value)'
                );

            Schema::connection(config('railforums.database_connection_name'))
                ->getConnection()
                ->getPdo()
                ->exec(
                    'ALTER TABLE ' . self::TABLE_NAME . ' ' .
                    'ADD FULLTEXT medium_full_text(medium_value)'
                );

            Schema::connection(config('railforums.database_connection_name'))
                ->getConnection()
                ->getPdo()
                ->exec(
                    'ALTER TABLE ' . self::TABLE_NAME . ' ' .
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
        Schema::dropIfExists(self::TABLE_NAME);
    }
}
