<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectInfoTypesTable extends Migration
{
    public function up()
    {
        Schema::create('projects_info_types', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade'); // Clé étrangère vers projects avec cascade
            $table->foreignId('info_type_id')->constrained('info_types')->onDelete('cascade'); // Clé étrangère vers info_types avec cascade
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects_info_types');
    }
}
