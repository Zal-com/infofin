<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects_organisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id');
            $table->foreignId('project_id');

            //Relations
            $table->foreign('organisation_id')->references('id')->on('organisations');
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects_organisations');
    }
};
