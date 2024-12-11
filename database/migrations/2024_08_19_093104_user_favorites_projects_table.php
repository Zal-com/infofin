<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users_favorite_projects', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade'); // Clé étrangère vers projects avec suppression en cascade
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Clé étrangère vers users avec suppression en cascade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_favorite_projects'); // Suppression de la table
    }
};
