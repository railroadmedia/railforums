<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumTableCategories extends Migration
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
                'forum_categories',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('title');
                    $table->string('slug');
                    $table->string('description')->nullable();
                    $table->integer('weight')->default(0);

                    $table->timestamps();
                    $table->softDeletes();
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
        Schema::dropIfExists('forum_categories');
    }
}
