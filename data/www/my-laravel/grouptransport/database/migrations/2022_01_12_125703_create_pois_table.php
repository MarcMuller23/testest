<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pois', function (Blueprint $table) {
            $table->string('poiId')->primary();
            $table->string('companyId');
            $table->string('poiName');
            $table->string('street');
            $table->string('houseNumber');
            $table->string('houseNumberAddition');
            $table->string('city');
            $table->string('postalCode');
            $table->string('country');
            $table->decimal('latitude');
            $table->decimal('longitude');
            $table->string('contactPerson');
            $table->string('phoneNumber');
            $table->time('openTime');
            $table->time('closeTime');
            $table->string('remark');
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
        Schema::dropIfExists('pois');
    }
}
