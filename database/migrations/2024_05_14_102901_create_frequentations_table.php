<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFrequentationTable extends Migration
{
    public function up()
    {
        Schema::create('frequentation', function (Blueprint $table) {
            $table->id('id');
            $table->integer('ProjectID');
            $table->timestamp('dateVisite')->default(DB::raw('CURRENT_TIMESTAMP'));
            // Timestamps gérés par la colonne dateVisite
        });
    }

    public function down()
    {
        Schema::dropIfExists('frequentation');
    }
}

