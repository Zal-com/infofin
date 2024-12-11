<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('projects_continents', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade'); // Clé étrangère vers projects
            $table->char('continent_code', 2); // Définit continent_code comme char(2)
            $table->foreign('continent_code')->references('code')->on('continents')->onDelete('cascade'); // Clé étrangère vers continents, champ 'code'

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_continent');
    }
};
