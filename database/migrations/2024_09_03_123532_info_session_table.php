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
            $table->id();
            $table->dateTime('session_datetime');
            $table->string("title");
            $table->text('description');
            $table->string('location')->nullable();
            $table->string('url')->nullable();
            $table->string('speaker')->nullable();
            $table->foreignId('organisation_id')->constrained('organisations');
            $table->timestamps();

            $table->foreign('organisation_id')->references('id')->on('organisations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('info_session');
    }
};
