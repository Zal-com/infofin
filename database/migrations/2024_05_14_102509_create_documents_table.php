<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->integer('DocumentID')->primary();
            $table->integer('ProjectID');
            $table->string('Name');
            $table->text('Description');
            $table->string('Filename');
            $table->timestamp('TimeStamp')->default(DB::raw('CURRENT_TIMESTAMP'));
            // Timestamps gérés par la colonne TimeStamp
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
}

