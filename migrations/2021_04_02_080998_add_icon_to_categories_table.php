<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class AddIconToCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConfigService::$databaseConnectionName)
            ->table(
                ConfigService::$tableCategories,
                function (Blueprint $table) {

                    $table->string('icon')->nullable();

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
        Schema::connection(ConfigService::$databaseConnectionName)
            ->table(
                ConfigService::$tableCategories,
                function (Blueprint $table) {

                    $table->dropColumn('icon');

                }
            );
    }
}
