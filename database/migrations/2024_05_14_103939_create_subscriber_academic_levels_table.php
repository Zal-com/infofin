<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersAcademicLevelsTable extends Migration
{
    public function up()
    {
        Schema::create('subscribers_academic_levels', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('SubscriberID');
            $table->integer('AcademicLevelID');
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscribers_academic_levels');
    }
}

