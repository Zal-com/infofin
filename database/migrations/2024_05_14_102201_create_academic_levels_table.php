<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcademicLevelsTable extends Migration
{
    public function up()
    {
        Schema::create('academic_levels', function (Blueprint $table) {
            $table->integer('AcademicLevelID')->primary();
            $table->string('Name')->index('Name');
            $table->tinyInteger('LangID')->default(1)->index('LangID');
            $table->integer('Order')->index('Order');
            $table->integer('Test');
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('academic_levels');
    }
}

