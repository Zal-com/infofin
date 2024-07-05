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
            $table->foreignId('sci_dom_cat_id');

            //Relations
            $table->foreign('sci_dom_cat_id')->references('id')->on('scientific_domain_categories');

        });
    }

    public function down()
    {
        Schema::dropIfExists('info_types');
    }
}
