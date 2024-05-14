<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRechercheTable extends Migration
{
    public function up()
    {
        Schema::create('recherche', function (Blueprint $table) {
            $table->id('Id');
            $table->integer('UserID');
            $table->timestamp('Time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('Recherche');
            // Pas de timestamps supplémentaires
        });
    }

    public function down()
    {
        Schema::dropIfExists('recherche');
    }
}

