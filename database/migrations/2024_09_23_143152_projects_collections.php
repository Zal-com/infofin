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

            // Utilisation de string pour collection_uid car ce n'est pas un id numérique
            $table->string('collection_uid'); // Clé étrangère vers collections.uid

            // Clés primaires composites pour éviter les doublons
            $table->primary(['project_id', 'collection_uid']);

            // Définition de la clé étrangère pour collection_uid
            $table->foreign('collection_uid')->references('uid')->on('collections')->onDelete('cascade');
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
