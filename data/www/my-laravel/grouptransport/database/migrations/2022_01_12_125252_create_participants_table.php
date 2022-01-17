<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {

            $table->string('participantId')->primary();
            $table->string('companyId');
            $table->string('labelId');
            $table->string('participantNumber');
            $table->string('name');
            $table->string('phoneNumber');
            $table->string('phoneNumber2');
            $table->string('email');
            $table->date('birthDate');
            $table->string('gender');
            $table->time('mon_arrival_time');
            $table->time('tue_arrival_time');
            $table->time('wed_arrival_time');
            $table->time('thu_arrival_time');
            $table->time('fri_arrival_time');
            $table->time('sat_arrival_time');
            $table->time('sun_arrival_time');
            $table->time('mon_departure_time');
            $table->time('tue_departure_time');
            $table->time('wed_departure_time');
            $table->time('thu_departure_time');
            $table->time('fri_departure_time');
            $table->time('sat_departure_time');
            $table->time('sun_departure_time');
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
        Schema::dropIfExists('participants');
    }
}
