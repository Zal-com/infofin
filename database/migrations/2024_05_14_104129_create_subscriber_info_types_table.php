<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersInfoTypesTable extends Migration
{
    public function up()
    {
        Schema::create('subscribers_info_types', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('SubscriberID');
            $table->integer('InfoTypeID');
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscribers_info_types');
    }
}

