<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersFacultiesTable extends Migration
{
    public function up()
    {
        Schema::create('users_faculties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('faculty_id');

            //Relations
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('faculty_id')->references('id')->on('faculties');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscribers_faculties');
    }
}

