<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectInfoTypesTable extends Migration
{
    public function up()
    {
        Schema::create('projects_info_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            $table->foreignId('info_type_id');

            //Relations
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('info_type_id')->references('id')->on('info_types');
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects_info_types');
    }
}

