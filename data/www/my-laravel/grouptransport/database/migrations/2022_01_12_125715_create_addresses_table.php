<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->string('addressId')->primary();
            $table->string('companyId');
            $table->string('poiId');
            $table->string('labelId');
            $table->string('addressName');
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
            $table->tinyInteger('active');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('addresses');
    }
}
