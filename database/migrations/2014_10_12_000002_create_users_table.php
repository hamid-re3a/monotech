<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->unsignedBigInteger('member_id')->unique()->nullable();
            $table->string('first_name',100);
            $table->string('last_name',100);
            $table->string('username',100);
            $table->unique('username','users_unique_username');
            $table->string('mobile_number',100)->nullable();
            $table->string('email',100)->index();
            $table->unique('email','users_unique_email');
            $table->string('gender')->nullable();
            $table->string('password');

            $table->string('block_type')->nullable();
            $table->string('block_reason')->nullable();

            $table->json('avatar')->nullable();

            $table->timestamp('email_verified_at')->nullable();

            $table->boolean('google2fa_enable')->default(false);
            $table->string('google2fa_secret')->nullable();

            $table->boolean('is_freeze')->default(FALSE)->nullable();
            $table->boolean('is_deactivate')->default(FALSE)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
