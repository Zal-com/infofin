<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersFacultiesTable extends Migration
{
    public function up()
    {
        Schema::create('users_faculties', function (Blueprint $table) {
            $table->id('id');
            $table->integer('user_id')->references("id")->on("users");
            $table->integer('faculty_id')->references('id')->on('faculties');
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('users_faculties');
    }
}

