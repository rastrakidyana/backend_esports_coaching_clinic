<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherParticipantModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_participants', function (Blueprint $table) {
            $table->id();
            $table->biginteger('id_peserta')->unsigned();
            $table->biginteger('id_pendaftaran_coaching_clinic')->unsigned();
            $table->string('nama_peserta_tambahan');
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
        Schema::dropIfExists('other_participant_models');
    }
}
