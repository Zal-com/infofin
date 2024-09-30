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
        Schema::create('collections', function (Blueprint $table) {
            $table->string('uid')->primary(); // Clé primaire de type string
            $table->string('name');
            $table->string('description')->nullable();
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->timestamps(); // Ajoute les colonnes created_at et updated_at
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
