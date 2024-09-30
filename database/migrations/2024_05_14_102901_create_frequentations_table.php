<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Import nécessaire pour DB::raw()

class CreateFrequentationsTable extends Migration
{
    public function up()
    {
        Schema::create('visits_rate', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade'); // Clé étrangère vers projects avec cascade
            $table->dateTime('date_visit')->default(DB::raw("CURRENT_TIMESTAMP")); // Date de visite avec timestamp par défaut
        });
    }

    public function down()
    {
        Schema::dropIfExists('visits_rate'); // Correction du nom de la table
    }
}
