<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtape5Table extends Migration
{
    public function up()
    {
        Schema::create('Etape5', function (Blueprint $table) {
            $table->id('id');
            $table->string('Email', 50)->primary();
            $table->string('ProjectID', 4)->nullable();
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('Etape5');
    }
}

