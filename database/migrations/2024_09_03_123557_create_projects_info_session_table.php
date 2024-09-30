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
        Schema::create('projects_info_sessions', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade'); // Clé étrangère vers projects avec cascade
            $table->foreignId('info_session_id')->constrained('info_sessions')->onDelete('cascade'); // Clé étrangère vers info_sessions avec cascade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects_info_sessions'); // Correction du nom de la table
    }
};
