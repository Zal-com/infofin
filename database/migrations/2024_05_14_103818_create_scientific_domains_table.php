<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScientificDomainsTable extends Migration
{
    public function up()
    {
        Schema::create('scientific_domains', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->string('name', 255); // Nom du domaine scientifique
            $table->foreignId('sci_dom_cat_id')->constrained('scientific_domain_categories')->onDelete('cascade'); // Clé étrangère vers scientific_domain_categories avec cascade
        });
    }

    public function down()
    {
        Schema::dropIfExists('scientific_domains');
    }
}
