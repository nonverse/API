<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recovery', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('email')->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('totp_token')->nullable();
            $table->boolean('email_belongs_to_user')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
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
        Schema::dropIfExists('recovery');
    }
};
