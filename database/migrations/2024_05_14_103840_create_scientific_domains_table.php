<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScientificDomainsTable extends Migration
{
    public function up()
    {
        Schema::create('scientific_domains', function (Blueprint $table) {
            $table->id('id');
            $table->string('name');
            $table->integer('sci_dom_cat_id')->references("id")->on("scientific_domains_category");
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('scientific_domains');
    }
}

