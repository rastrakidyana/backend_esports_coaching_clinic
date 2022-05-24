<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyCoachingClinicModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_coaching_clinics', function (Blueprint $table) {
            $table->id();
            $table->biginteger('id_coaching_clinic')->unsigned();
            $table->biginteger('id_jadwal_coaching_clinic')->unsigned();
            $table->biginteger('id_peserta')->unsigned();
            $table->biginteger('id_penilaian_coach')->unsigned();
            $table->string('status_my_coaching_clinic');
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
        Schema::dropIfExists('my_coaching_clinic_models');
    }
}
