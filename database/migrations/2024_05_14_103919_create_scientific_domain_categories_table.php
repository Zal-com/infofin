<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScientificDomainsCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('scientific_domains_categories', function (Blueprint $table) {
            $table->id('CategoryID');
            $table->string('Name');
            $table->tinyInteger('LangID')->default(1);
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('scientific_domains_categories');
    }
}

