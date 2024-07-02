<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectScientificDomainsTable extends Migration
{
    public function up()
    {
        Schema::create('projects_scientific_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            $table->foreignId('scientific_domain_id');

            //Relations
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('scientific_domain_id')->references('id')->on('scientific_domains');
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects_scientific_domains');
    }
}

