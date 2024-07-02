<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfoTypesTable extends Migration
{
    public function up()
    {
        Schema::create('info_types', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->foreignId('category_id');
            // Pas de timestamps

            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    public function down()
    {
        Schema::dropIfExists('info_types');
    }
}
