<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSciAcadMailTable extends Migration
{
    public function up()
    {
        Schema::create('SciAcadMail', function (Blueprint $table) {
            $table->string('Matricule', 6)->primary();
            $table->text('Email')->nullable();
            $table->text('corps_electoral')->nullable();
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('SciAcadMail');
    }
}

