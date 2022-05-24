<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->biginteger('id_coach')->unsigned();
            $table->double('total_pembayaran');
            $table->time('countdown');
            $table->longtext('gambar_bukti_pembayaran');
            $table->string('nama_pengirim');
            $table->string('nama_bank_pengirim');
            $table->integer('no_rek_pengirim');
            $table->double('nominal_pembayaran');
            $table->boolean('is_confirmed')->default(0);
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
        Schema::dropIfExists('payment_models');
    }
}
