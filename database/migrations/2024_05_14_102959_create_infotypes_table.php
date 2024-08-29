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
            $table->foreignId('info_types_cat_id')->constrained('info_types_categories');

        });
    }

    public function down()
    {
        Schema::dropIfExists('info_types');
    }
}
