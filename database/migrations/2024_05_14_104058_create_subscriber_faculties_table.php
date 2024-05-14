<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersFacultiesTable extends Migration
{
    public function up()
    {
        Schema::create('subscribers_faculties', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('SubscriberID');
            $table->integer('FacultyID');
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscribers_faculties');
    }
}

