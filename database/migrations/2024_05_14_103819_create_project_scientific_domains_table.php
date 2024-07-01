<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsScientificDomainsTable extends Migration
{
    public function up()
    {
        Schema::create('projects_scientific_domains', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->references('id')->on("projects");
            $table->integer('scientific_domain_id')->references('id')->on("scientific_domain");
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects_scientific_domains');
    }
}

