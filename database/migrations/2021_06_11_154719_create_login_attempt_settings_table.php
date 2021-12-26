<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginAttemptSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_attempt_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('times');
            $table->integer('duration');
            $table->integer('priority');
            $table->integer('blocking_duration');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('login_attempt_settings');
    }
}
