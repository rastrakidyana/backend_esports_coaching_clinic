<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegisterCoachingClinicModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('register_coaching_clinics', function (Blueprint $table) {
            $table->id();
            $table->biginteger('id_peserta')->unsigned();
            $table->biginteger('id_my_coaching_clinic')->unsigned()->nullable();
            $table->biginteger('id_coaching_clinic')->unsigned();
            $table->biginteger('id_jadwal_coaching_clinic')->unsigned();
            $table->biginteger('id_pembayaran')->unsigned()->nullable();
            $table->integer('jml_beli_kuota');
            $table->double('harga_pendaftaran');
            $table->string('status_pendaftaran');
            $table->boolean('is_my')->default(0);
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
        Schema::dropIfExists('register_coaching_clinic_models');
    }
}
