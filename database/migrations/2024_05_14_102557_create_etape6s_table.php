<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtape6Table extends Migration
{
    public function up()
    {
        Schema::create('Etape6', function (Blueprint $table) {
            $table->string('Email', 50)->primary();
            $table->string('P1', 4)->nullable();
            $table->string('P2', 4)->nullable();
            $table->string('P3', 4)->nullable();
            $table->string('P4', 4)->nullable();
            $table->string('P5', 4)->nullable();
            $table->string('P6', 4)->nullable();
            $table->string('P7', 4)->nullable();
            $table->string('P8', 4)->nullable();
            $table->string('P9', 4)->nullable();
            $table->string('P10', 4)->nullable();
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('Etape6');
    }
}

