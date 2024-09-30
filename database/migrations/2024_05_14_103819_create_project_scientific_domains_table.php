<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectScientificDomainsTable extends Migration
{
    public function up()
    {
        Schema::create('projects_scientific_domains', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade'); // Clé étrangère vers projects avec suppression en cascade
            $table->foreignId('scientific_domain_id')->constrained('scientific_domains')->onDelete('cascade'); // Clé étrangère vers scientific_domains avec suppression en cascade
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects_scientific_domains');
    }
}
