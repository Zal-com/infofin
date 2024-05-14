<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsAcademicLevelsTable extends Migration
{
    public function up()
    {
        Schema::create('projects_academic_levels', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('ProjectID');
            $table->integer('AcademicLevelID');
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects_academic_levels');
    }
}
