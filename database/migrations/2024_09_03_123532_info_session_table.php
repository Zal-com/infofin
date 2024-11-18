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
        Schema::create('info_sessions', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->dateTime('session_datetime'); // Date et heure de la session
            $table->string('title'); // Titre de la session
            $table->text('description'); // Description de la session
            $table->string('location')->nullable(); // Lieu, peut être nul
            $table->string('url')->nullable(); // URL, peut être nul
            $table->string('speaker')->nullable(); // Nom du speaker, peut être nul
            $table->boolean("status")->default(true)->comment("0 : inactive, 1 : active");
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade'); // Clé étrangère vers organisations avec cascade
            $table->smallInteger('session_type'); // 0 = Distanciel, 1 = Présentiel, 2 = Hybride
            $table->timestamps(); // created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('info_sessions'); // Correction du nom de la table
    }
};
