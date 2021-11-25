<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('minecraft')->create('profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->uuid('mc_uuid');
            $table->string('mc_username');
            $table->integer('rank')->default(1);
            $table->string('group')->default('default');
            $table->text('teams')->nullable();
            $table->timestamp('profile_verified_at')->nullable();
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
        Schema::connection('minecraft')->dropIfExists('profiles');
    }
}
