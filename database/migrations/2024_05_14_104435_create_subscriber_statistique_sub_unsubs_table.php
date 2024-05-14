<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersStatistiquesSubUnsubTable extends Migration
{
    public function up()
    {
        Schema::create('subscribers_statistiques_sub_unsub', function (Blueprint $table) {
            $table->id('SubscriberID');
            $table->enum('action', ['subscribe', 'unsubscribe']);
            $table->timestamp('TimeStamp')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->primary(['SubscriberID', 'action', 'TimeStamp']);
            // Pas de timestamps car géré manuellement avec 'TimeStamp'
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscribers_statistiques_sub_unsub');
    }
}

