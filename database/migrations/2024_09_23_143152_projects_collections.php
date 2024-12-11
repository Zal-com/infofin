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
            // Utilisation de foreignId pour project_id
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');

            // Utilisation de uuid pour collection_id car il fait référence à un uuid
            $table->uuid('collection_id'); // Clé étrangère vers collections.id

            // Clés primaires composites pour éviter les doublons
            $table->primary(['project_id', 'collection_id']);

            // Définitions des clés étrangères
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
