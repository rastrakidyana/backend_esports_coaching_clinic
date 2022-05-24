<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoachAssessmentModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coach_assessments', function (Blueprint $table) {
            $table->id();
            $table->biginteger('id_coach')->unsigned();
            $table->biginteger('id_peserta')->unsigned();
            $table->biginteger('id_my_coaching_clinic')->unsigned();
            $table->double('rate');
            $table->string('komentar');
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
        Schema::dropIfExists('coach_assessment_models');
    }
}
