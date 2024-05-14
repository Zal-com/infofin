<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScientificDomainsTable extends Migration
{
    public function up()
    {
        Schema::create('scientific_domains', function (Blueprint $table) {
            $table->id('ScientificDomainID');
            $table->string('Name');
            $table->integer('CategoryID');
            $table->tinyInteger('LangID')->default(1);
            $table->integer('Order');
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('scientific_domains');
    }
}

