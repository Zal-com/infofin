<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade'); // Clé étrangère vers projects avec cascade
            $table->string('filename', 255); // Nom du fichier
            $table->string('path', 255); // Chemin du fichier
            $table->timestamps(); // Colonnes created_at et updated_at
            $table->integer('download_count')->default(0); // Compteur de téléchargements par défaut 0
            $table->boolean('is_draft')->default(false); // Indicateur de brouillon par défaut false
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
