<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersInfoTypesTable extends Migration
{
    public function up()
    {
        Schema::create('users_info_types', function (Blueprint $table) {
            $table->id('id');
            $table->integer('user_id')->references("id")->on("users");
            $table->integer('info_type_id')->references("id")->on("info_types");
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('users_info_types');
    }
}

