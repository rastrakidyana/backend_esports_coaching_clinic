<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleCoachingClinicModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_coaching_clinics', function (Blueprint $table) {
            $table->id();
            $table->biginteger('id_coaching_clinic')->unsigned();
            $table->date('tgl_coaching_clinic');
            $table->time('mulai_coaching_clinic');
            $table->time('berakhir_coaching_clinic')->nullable;
            $table->integer('kuota_coaching_clinic');
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
        Schema::dropIfExists('schedule_coaching_clinic_models');
    }
}
