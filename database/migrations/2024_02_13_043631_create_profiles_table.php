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
        Schema::connection('labs.minecraft')->create('profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('username');
            $table->uuid('mc_uuid');
            $table->timestamp('profile_verified_at');
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
        Schema::connection('labs.minecraft')->dropIfExists('profiles');
    }
};
