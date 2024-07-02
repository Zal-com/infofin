<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersInfoTypesTable extends Migration
{
    public function up()
    {
        Schema::create('users_info_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('info_type_id');

            //Relations
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('info_type_id')->references('id')->on('info_types');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscribers_info_types');
    }
}

