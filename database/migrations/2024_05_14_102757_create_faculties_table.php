<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacultiesTable extends Migration
{
    public function up()
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->integer('FacultyID')->primary();
            $table->string('Name')->index('Name');
            $table->tinyInteger('LangID')->default(1)->index('LangID');
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('faculties');
    }
}

