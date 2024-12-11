<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('projects_countries', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade'); // Clé étrangère vers projects
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade'); // Clé étrangère vers countries

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_country');
    }
};
