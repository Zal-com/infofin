<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsKeywordsTable extends Migration
{
    public function up()
    {
        Schema::create('projects_keywords', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('ProjectID');
            $table->integer('KeywordID');
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects_keywords');
    }
}
