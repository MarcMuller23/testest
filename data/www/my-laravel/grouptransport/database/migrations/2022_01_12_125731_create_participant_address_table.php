<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participant_address', function (Blueprint $table) {
            $table->id();
            $table->string('participantId');
            $table->string('addressId');
            $table->integer('marge');
            $table->integer('maxTravelTime');
            $table->date('startDate');
            $table->date('endDate');
            $table->tinyInteger('monday');
            $table->tinyInteger('tuesday');
            $table->tinyInteger('wednesday');
            $table->tinyInteger('thursday');
            $table->tinyInteger('friday');
            $table->tinyInteger('saturday');
            $table->tinyInteger('sunday');
            $table->tinyInteger('active');
            $table->dateTime('created_at');
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
        Schema::dropIfExists('participant_address');
    }
}
