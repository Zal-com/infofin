<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrldescriptionTable extends Migration
{
    public function up()
    {
        Schema::create('urldescription', function (Blueprint $table) {
            $table->id('id');
            $table->string('urls', 10000);
            $table->string('location', 10000);
            $table->integer('numinfo');
            $table->string('nomtable', 255)->nullable();
            $table->date('date');
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('urldescription');
    }
}

