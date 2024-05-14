<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsInfoTypesTable extends Migration
{
    public function up()
    {
        Schema::create('projects_info_types', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('ProjectID');
            $table->integer('InfoTypeID');
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects_info_types');
    }
}

