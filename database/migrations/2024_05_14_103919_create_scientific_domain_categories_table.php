<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScientificDomainsCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('scientific_domain_categories', function (Blueprint $table) {
           $table->id();
           $table->string('name', 255);
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('scientific_domains_categories');
    }
}

