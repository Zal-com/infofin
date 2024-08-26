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
        Schema::create('newsletter_schedule', function (Blueprint $table) {
            $table->id();
            $table->string('day_of_week'); // Stocker le jour de la semaine (0 = Sunday, 1 = Monday, etc.)
            $table->time('send_time');     // Stocker l'heure d'envoi
            $table->boolean('is_active')->default(true);
            $table->string('message')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_schedule');
    }
};
