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
        Schema::create('projects_collections', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id'); // Clé étrangère vers projects.id
            $table->string('collection_id');         // Clé étrangère vers collections.uid

            // Clés primaires composites pour éviter les doublons
            $table->primary(['project_id', 'collection_id']);

            // Définitions des clés étrangères
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('collection_id')->references('id')->on('collections')->onDelete('cascade');
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects_collections');
    }
};
