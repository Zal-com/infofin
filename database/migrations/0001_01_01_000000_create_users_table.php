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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('UserID');
            $table->tinyInteger('Type')->default(2);
            $table->string('Username', 50);
            $table->string('Password', 50);
            $table->timestamp('TimeStamp')->default(DB::raw('CURRENT_TIMESTAMP'));
            // Pas d'autres timestamps nécessaires
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
