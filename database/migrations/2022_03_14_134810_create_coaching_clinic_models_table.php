<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoachingClinicModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coaching_clinics', function (Blueprint $table) {
            $table->id();
            $table->biginteger('id_coach')->unsigned();
            $table->string('judul_coaching_clinic');
            $table->string('game_coaching_clinic');
            $table->double('harga_coaching_clinic');
            $table->string('desc_coaching_clinic')->nullable();
            $table->string('tipe_coaching_clinic');
            $table->string('at_coaching_clinic');
            $table->string('link_coaching_clinic')->nullable();
            $table->longtext('gambar_coaching_clinic')->nullable();
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
        Schema::dropIfExists('coaching_clinic_models');
    }
}
