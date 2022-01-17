<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participant_group', function (Blueprint $table) {
            $table->id();
            $table->string('groupId');
            $table->string('participantId');
            $table->string('fromAddressId');
            $table->string('toAddressId');
            $table->tinyInteger('active');
            $table->dateTime('create_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('participant_group');
    }
}
