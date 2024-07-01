<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRechercheTable extends Migration
{
    public function up()
    {
        Schema::create('searches', function (Blueprint $table) {
            $table->id('id');
            $table->integer('user_id')->references('id')->on("users")->onDelete("CASCADE");
            $table->timestamp('Time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('Recherche');
            // Pas de timestamps supplémentaires
        });
    }

    public function down()
    {
        Schema::dropIfExists('searches');
    }
}

