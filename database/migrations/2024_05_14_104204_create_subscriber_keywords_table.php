<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersKeywordsTable extends Migration
{
    public function up()
    {
        Schema::create('subscribers_keywords', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('SubscriberID');
            $table->integer('KeywordID');
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscribers_keywords');
    }
}

