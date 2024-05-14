<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFicheDelTable extends Migration
{
    public function up()
    {
        Schema::create('ficheDel', function (Blueprint $table) {
            $table->id('id');
            $table->integer('numFiche');
            $table->timestamp('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('personne');
            // Timestamps gérés par la colonne date
        });
    }

    public function down()
    {
        Schema::dropIfExists('ficheDel');
    }
}

