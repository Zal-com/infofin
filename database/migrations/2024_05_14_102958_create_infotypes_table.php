<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfoTypesTable extends Migration
{
    public function up()
    {
        Schema::create('info_types', function (Blueprint $table) {
            $table->integer('InfoTypeID')->primary();
            $table->string('Name')->index("Name");
            $table->integer('CategoryID')->index("CategoryID");
            $table->tinyInteger('LangID')->default(1)->index("LangID");
            $table->integer('Order')->index("Order");
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('info_types');
    }
}
