<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class CreateForumsTablePostReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConfigService::$databaseConnectionName)
            ->create(
                ConfigService::$tablePostReports,
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('post_id')->unsigned();
                    $table->integer('reporter_id')->unsigned();
                    $table->dateTime('reported_on');

                    $table->timestamps();

                    $table->index(['post_id']);
                    $table->index(['reporter_id']);
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
        Schema::dropIfExists(ConfigService::$tablePostReports);
    }
}
