<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('ip_id')->nullable();
            $table->foreign('ip_id')->references('id')->on('ips')->onDelete('cascade');

            $table->unsignedBigInteger('agent_id')->nullable();
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');

            $table->tinyInteger('login_status')->default(LOGIN_ATTEMPT_STATUS_ON_GOING);
            $table->boolean('is_from_new_device')->default(false);

            $table->tinyInteger('blocked_tier')->nullable()->default(null);

            $table->softDeletes();
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
        Schema::dropIfExists('login_attempts');
    }
}
