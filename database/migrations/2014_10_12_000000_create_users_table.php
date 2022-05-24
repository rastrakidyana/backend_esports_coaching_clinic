<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coachs', function (Blueprint $table) {
            $table->id();
            $table->biginteger('id_bank_account')->unsigned()->nullable();
            $table->string('nama_coach');
            $table->string('email_coach')->unique();
            $table->string('password_coach');
            $table->string('domisili_coach');
            $table->string('telp_coach');
            $table->string('jk_coach');
            $table->string('tgl_lahir_coach');
            $table->string('desc_coach');
            $table->integer('total_rate');
            $table->longtext('foto_profil_coach');
            $table->boolean('is_verified')->default(0);            
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
