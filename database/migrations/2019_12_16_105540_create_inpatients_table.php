<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInpatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inpatients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('ward_id');
            $table->foreign('ward_id')->references('id')->on('wards');
            $table->string('patient_inventory')->nullable();
            $table->string('disease');
            $table->integer('duration');
            $table->string('condition');
            $table->string('certified_officer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inpatients');
    }
}
