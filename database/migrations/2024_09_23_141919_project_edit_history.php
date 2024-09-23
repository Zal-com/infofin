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
            $table->unsignedBigInteger('id_project'); // Référence à projects.id
            $table->unsignedBigInteger('id_user'); // Référence à users.id

            // Définition des clés étrangères
            $table->foreign('id_project')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
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
