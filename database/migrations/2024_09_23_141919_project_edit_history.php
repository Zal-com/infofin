<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Exécute les migrations.
     */
    public function up(): void
    {
        Schema::create('projects_edit_history', function (Blueprint $table) {
            $table->id();// Clé primaire auto-incrémentée nommée 'uid'
            $table->dateTime('date'); // Colonne date et heure de l'édition
            $table->unsignedBigInteger('project_id'); // Référence à projects.id
            $table->unsignedBigInteger('user_id'); // Référence à users.id

            // Définition des clés étrangères
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects_edit_history');
    }
};
