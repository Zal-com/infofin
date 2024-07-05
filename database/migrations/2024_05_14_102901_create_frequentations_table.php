<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFrequentationsTable extends Migration
{
    public function up()
    {
        Schema::create('visits_rate', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            $table->dateTime('date_visit')->default(DB::raw("CURRENT_TIMESTAMP"));

            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    public function down()
    {
        Schema::dropIfExists('frequentation');
    }
}

