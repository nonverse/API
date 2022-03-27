<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthMesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('minecraft')->create('authme', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('realname');
            $table->string('email');
            $table->string('password');
            $table->string('ip', 40)->nullable();
            $table->bigInteger('last_login')->nullable();
            $table->string('world')->nullable();
            $table->double('x')->default(0);
            $table->double('y')->default(0);
            $table->double('z')->default(0);
            $table->float('yaw')->nullable();
            $table->float('pitch')->nullable();
            $table->string('totp')->nullable();
            $table->smallInteger('is_logged')->nullable();
            $table->smallInteger('has_session')->default(0);
            $table->bigInteger('reg_date');
            $table->string('reg_ip', 40);
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
        Schema::dropIfExists('authme');
    }
}
